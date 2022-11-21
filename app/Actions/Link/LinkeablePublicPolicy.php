<?php

namespace App\Actions\Link;

use App\Biolink;
use App\Exceptions\LinkRedirectFailed;
use App\Link;
use App\LinkDomain;
use App\LinkGroup;
use App\LinkeableRule;
use App\Notifications\ClickQuotaExhausted;
use Carbon\Carbon;
use Common\Core\AppUrl;
use Common\Domains\CustomDomain;
use Common\Settings\Settings;
use Illuminate\Notifications\DatabaseNotification;
use Str;
use Swift_TransportException;

class LinkeablePublicPolicy
{
    /**
     * @var Settings
     */
    private $settings;
    /**
     * @var AppUrl
     */
    private $appUrl;
    /**
     * @var LinkDomain
     */
    private $customDomain;
    /**
     * @var string
     */
    private $modelName;

    public function __construct(
        Settings $settings,
        AppUrl $appUrl,
        LinkDomain $customDomain
    ) {
        $this->settings = $settings;
        $this->appUrl = $appUrl;
        $this->customDomain = $customDomain;
    }

    /**
     * @param Link|LinkGroup $model
     */
    public function isAccessible($model): bool
    {
        $this->modelName = (string) Str::of(get_class($model))
            ->classBasename()
            ->snake()
            ->replace('_', ' ')
            ->title();

        return $this->isValidDomain($model) &&
            !$this->pastExpirationDateOrClicks($model) &&
            !$this->overClickQuota($model);
    }

    /**
     * @param Link|LinkGroup|Biolink $linkeable
     * @return bool
     */
    public static function linkeableExpired($linkeable): bool
    {
        return $linkeable->expires_at && $linkeable->expires_at->lessThan(now());
    }

    /**
     * @param Link|LinkGroup|Biolink $linkeable
     * @return bool
     */
    public static function linkeableWillActivateLater($linkeable): bool
    {
        return $linkeable->activates_at && $linkeable->activates_at->isAfter(now());
    }

    /**
     * @param LinkGroup|Link $link
     */
    private function pastExpirationDateOrClicks($link): bool
    {
        if (static::linkeableExpired($link)) {
            throw (new LinkRedirectFailed(
                "$this->modelName is past its expiration date ($link->expires_at)",
            ))->setModel($link);
        }

        if (static::linkeableWillActivateLater($link)) {
            throw (new LinkRedirectFailed(
                "$this->modelName is set to activate on ($link->activates_at)",
            ))->setModel($link);
        }

        $expClicksRule = $link->rules->first(function (LinkeableRule $rule) {
            return $rule->type === 'exp_clicks';
        });
        if (
            $expClicksRule &&
            (int) $expClicksRule->key <= $link->clicks_count
        ) {
            $msg = "$this->modelName is past it's specified expiration clicks ($expClicksRule->key).";
            if ($expClicksRule->value) {
                $msg .= " Visits to this $this->modelName will redirect to '$expClicksRule->value'.";
            }

            throw (new LinkRedirectFailed($msg))
                ->setModel($link)
                ->setRedirectUrl($expClicksRule->value);
        }

        return false;
    }

    /**
     * @param Link|LinkGroup $model
     */
    private function isValidDomain($model): bool
    {
        $defaultHost =
            $this->settings->get('custom_domains.default_host') ?:
            config('app.url');
        $defaultHost = $this->appUrl->getHostFrom($defaultHost);
        $requestHost = $this->appUrl->getRequestHost();

        // link should only be accessible via single domain
        if ($model->domain_id > 0) {
            $domain = $this->customDomain
                ->forUser($model->user_id)
                ->find($model->domain_id);
            if (!$domain || !$this->appUrl->requestHostMatches($domain->host)) {
                throw (new LinkRedirectFailed(
                    "$this->modelName is set to only be accessible via '$domain->host', but request domain is '$requestHost'",
                ))->setModel($model);
            }
        }

        // link should be accessible via default domain only
        elseif ($model->domain_id === 0) {
            if (!$this->appUrl->requestHostMatches($defaultHost)) {
                throw (new LinkRedirectFailed(
                    "$this->modelName is set to only be accessible via '$defaultHost' (default domain), but request domain is '$requestHost'",
                ))->setModel($model);
            }
        }

        // link should be accessible via default and all user connected domains
        else {
            if ($this->appUrl->requestHostMatches($defaultHost, true)) {
                return true;
            }
            $domains = $this->customDomain->forUser($model->user_id)->get();
            $customDomainMatches = $domains->contains(function (
                CustomDomain $domain
            ) {
                return $this->appUrl->requestHostMatches($domain->host);
            });
            if (!$customDomainMatches) {
                throw (new LinkRedirectFailed(
                    "Current domain '$requestHost' does not match default domain or any custom domains connected by user.",
                ))->setModel($model);
            }
        }

        return true;
    }

    /**
     * @param LinkGroup|Link $link
     */
    private function overClickQuota($link): bool
    {
        // link might not be attached to user
        if (!$link->user) {
            return false;
        }
        $quota = $link->user->getRestrictionValue(
            'links.create',
            'click_count',
        );
        if (is_null($quota)) {
            return false;
        }

        $totalClicks = app(GetMonthlyClicks::class)->execute($link->user);

        if ($quota < $totalClicks) {
            $alreadyNotifiedThisMonth = app(DatabaseNotification::class)
                ->where('type', ClickQuotaExhausted::class)
                ->whereBetween('created_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ])
                ->exists();
            if (!$alreadyNotifiedThisMonth) {
                try {
                    $link->user->notify(new ClickQuotaExhausted());
                } catch (Swift_TransportException $e) {
                    //
                }
            }
            throw (new LinkRedirectFailed(
                "User this $this->modelName belongs to is over their click quota for the month.",
            ))->setModel($link);
        }

        return false;
    }
}
