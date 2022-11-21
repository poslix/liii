<?php

namespace App\Actions\Link;

use App\Biolink;
use App\Link;
use App\LinkeableClick;
use App\LinkGroup;
use App\User;
use Arr;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Common\Core\Values\ValueLists;
use Common\Workspaces\Workspace;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GenerateLinkReport
{
    /**
     * @var CarbonPeriod
     */
    private $range;

    /**
     * @var Builder
     */
    private $query;

    /**
     * @var LinkeableClick
     */
    private $linkClick;

    public function __construct(LinkeableClick $linkClick)
    {
        $this->linkClick = $linkClick;
    }

    /**
     * @param Link|LinkGroup|User|Workspace $model
     */
    public function execute(array $params, $model = null): array
    {
        if (is_a($model, Link::class)) {
            $this->query = $model->clicks();
        } elseif (is_a($model, LinkGroup::class)) {
            $this->query = $this->groupClicksQuery($model);
        } elseif (is_a($model, User::class)) {
            $this->query = $this->linkClick->where('owner_id', $model->id);
        } else {
            $this->query = $this->linkClick->newQuery();
        }

        $clicks = $this->getClickData($params);
        $totalClicks = $clicks->sum('count');
        return [
            'clicks' => $clicks,
            'totalClicks' => $totalClicks,
            'devices' => $this->getData('device'),
            'browsers' => $this->getData('browser'),
            'platforms' => $this->getData('platform'),
            'locations' => $this->getLocationData($totalClicks),
            'referrers' => $this->getData('referrer'),
            'startDate' => $this->range->getStartDate()->toJSON(),
            'endDate' => $this->range->getStartDate()->toJSON(),
        ];
    }

    /**
     * @param int $totalClicks
     * @return Collection
     */
    private function getLocationData($totalClicks)
    {
        $locations = $this->getData('location');
        $countries = app(ValueLists::class)->countries();
        return $locations->map(function ($location) use (
            $countries,
            $totalClicks
        ) {
            // only short country code is stored in DB, get and return full country name as well
            $location['code'] = strtolower($location['label']);
            $location['label'] = Arr::first($countries, function (
                $country
            ) use ($location) {
                return $country['code'] === $location['code'];
            })['name'];
            // add percentage of total for each country
            $location['percentage'] = round(
                (100 * $location['count']) / $totalClicks,
                1,
            );
            return $location;
        });
    }

    private function getClickData(array $params): Collection
    {
        $range = Arr::get($params, 'range', 'weekly');
        $prefix = DB::getTablePrefix();

        if (
            $range === 'custom' &&
            ($customRange = Arr::get($params, 'customRange'))
        ) {
            [$start, $end] = explode(':', $customRange);
            $this->range = CarbonPeriod::create(
                Carbon::parse($start),
                Carbon::parse($end),
            );
            $clicks = $this->getData(
                DB::raw("DAY({$prefix}link_clicks.created_at) as label"),
            )->map(function ($click) {
                $click['label'] = Carbon::createFromTimestamp(
                    $click['timestamp'],
                )->format('Y-m-d');
                return $click;
            });

            foreach ($this->range as $date) {
                $this->maybePushLabel(
                    $clicks,
                    $date->format('Y-m-d'),
                    $date->timestamp,
                );
            }

            $clicks = $clicks->sortBy('timestamp');
        } elseif ($range === 'yearly') {
            $this->range = CarbonPeriod::create(
                Carbon::now()->startOfYear(),
                '1 month',
                Carbon::now()->endOfYear(),
            );
            $clicks = $this->getData(
                DB::raw("MONTH({$prefix}link_clicks.created_at) as label"),
            );

            foreach ($this->range as $date) {
                $this->maybePushLabel($clicks, $date->month, $date->timestamp);
            }

            // sort by month and format label to "Jan"
            $clicks = $clicks->sortBy('label')->map(function ($click) {
                $click['label'] = Carbon::createFromFormat(
                    'm',
                    $click['label'],
                )->shortLocaleMonth;
                return $click;
            });
        } elseif ($range === 'monthly') {
            $this->range = CarbonPeriod::create(
                Carbon::now()
                    ->startOfMonth()
                    ->startOfDay(),
                Carbon::now()
                    ->endOfMonth()
                    ->endOfDay(),
            );
            $clicks = $this->getData(
                DB::raw("DAY({$prefix}link_clicks.created_at) as label"),
            );

            foreach ($this->range as $date) {
                $this->maybePushLabel($clicks, $date->day, $date->timestamp);
            }

            // sort by day and format date to "01"
            $clicks = $clicks->sortBy('label')->map(function ($click) {
                $click['label'] = Carbon::createFromFormat(
                    'd',
                    $click['label'],
                )->format('d');
                return $click;
            });
        } elseif ($range === 'hourly') {
            $this->range = CarbonPeriod::create(
                Carbon::now()->startOfDay(),
                '1 hour',
                Carbon::now()->endOfDay(),
            );
            $clicks = $this->getData(
                DB::raw("HOUR({$prefix}link_clicks.created_at) as label"),
            );

            foreach ($this->range as $date) {
                $this->maybePushLabel($clicks, $date->hour, $date->timestamp);
            }

            // sort by hour and format date to "24:00"
            $clicks = $clicks->sortBy('label')->map(function ($click) {
                $click['label'] = Carbon::createFromFormat(
                    'H',
                    $click['label'],
                )->format('H:i');
                return $click;
            });
        } else {
            $this->range = CarbonPeriod::create(
                Carbon::now()
                    ->startOfWeek()
                    ->startOfDay(),
                Carbon::now()
                    ->endOfWeek()
                    ->endOfDay(),
            );
            $clicks = $this->getData(
                DB::raw("DAY({$prefix}link_clicks.created_at) as label"),
            );

            foreach ($this->range as $date) {
                $this->maybePushLabel($clicks, $date->day, $date->timestamp);
            }

            // sort by day and format date to "Wen, 05"
            $clicks = $clicks->sortBy('timestamp')->map(function ($click) {
                $click['label'] = Carbon::createFromTimestamp(
                    $click['timestamp'],
                )->format('d, D');
                return $click;
            });
        }

        return $clicks->values();
    }

    private function maybePushLabel(Collection $clicks, $label, int $timestamp)
    {
        $contains = $clicks->first(function ($click) use ($label) {
            return $click['label'] === $label;
        });
        if (!$contains) {
            $clicks->push([
                'label' => $label,
                'count' => 0,
                'timestamp' => $timestamp,
            ]);
        }
    }

    private function getData($select)
    {
        return $this->query
            ->where('crawler', false)
            ->whereBetween('link_clicks.created_at', [
                $this->range->getStartDate(),
                $this->range->getEndDate(),
            ])
            ->select([
                is_string($select) ? "$select as label" : $select,
                DB::raw('COUNT(*) as count'),
                'link_clicks.created_at as timestamp',
            ])
            ->groupBy('label')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($click) {
                $click['timestamp'] = Carbon::parse(
                    $click['timestamp'],
                )->timestamp;
                return $click;
            });
    }

    /**
     * @param LinkGroup|Biolink $group
     */
    private function groupClicksQuery($group)
    {
        $ids = $group->links()->pluck('links.id');
        return $this->linkClick
            ->whereIn('linkeable_id', $ids)
            ->where('linkeable_type', Link::class);
    }
}
