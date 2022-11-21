<?php

namespace App;

use App\Workspaces\WorkspaceRelationships;
use Carbon\Carbon;
use Common\Auth\BaseUser;
use Common\Auth\Roles\Role;
use Common\Auth\SocialProfile;
use Common\Billing\Subscription;
use Common\Domains\CustomDomain;
use Common\Files\FileEntry;
use Common\Workspaces\Workspace;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\User
 *
 * @property int $id
 * @property string|null $username
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $avatar_url
 * @property string|null $gender
 * @property string $email
 * @property string|null $password
 * @property string|null $card_brand
 * @property string|null $card_last_four
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $confirmed
 * @property string|null $confirmation_code
 * @property string|null $language
 * @property string|null $country
 * @property string|null $timezone
 * @property string $avatar
 * @property string|null $stripe_id
 * @property int $available_space
 * @property-read Collection|FileEntry[] $entries
 * @property-read string $display_name
 * @property-read bool $has_password
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read Collection|Role[] $roles
 * @property-read Collection|SocialProfile[] $social_profiles
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read Collection|CustomDomain[] $custom_domains
 * @property-read Collection|Link[] $links
 * @property-read Collection|LinkOverlay[] $link_overlays
 * @property-read array $permissions
 * @mixin Eloquent
 * @property string|null $legacy_permissions
 * @property string|null $api_token
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property-read Collection|\App\LinkeableClick[] $linkClicks
 * @property-read int|null $link_clicks_count
 * @property-read Collection|\App\LinkDomain[] $linkDomains
 * @property-read int|null $link_domains_count
 * @property-read Collection|\App\LinkGroup[] $linkGroups
 * @property-read int|null $link_groups_count
 * @property-read Collection|\App\LinkOverlay[] $linkOverlays
 * @property-read int|null $link_overlays_count
 * @property-read Collection|\App\LinkPage[] $linkPages
 * @property-read int|null $links_count
 * @property-read Collection|\Common\Notifications\NotificationSubscription[] $notificationSubscriptions
 * @property-read int|null $notification_subscriptions_count
 * @property-read int|null $notifications_count
 * @property-read int|null $permissions_count
 * @property-read int|null $roles_count
 * @property-read int|null $social_profiles_count
 * @property-read int|null $subscriptions_count
 * @property-read Collection|\App\TrackingPixel[] $trackingPixels
 * @property-read int|null $tracking_pixels_count
 * @property-read Collection|Workspace[] $workspaces
 * @property-read int|null $workspaces_count
 * @method static Builder|BaseUser basicSearch(string $query)
 * @method static Builder|BaseUser compact()
 */
class User extends BaseUser
{
    use WorkspaceRelationships, HasFactory, HasApiTokens;

    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class, 'owner_id');
    }
}
