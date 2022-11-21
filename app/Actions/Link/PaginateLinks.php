<?php

namespace App\Actions\Link;

use App\Link;
use Arr;
use Common\Database\Datasource\Datasource;
use Illuminate\Database\Eloquent\Builder;
use Str;

class PaginateLinks
{
    /**
     * @var Link
     */
    private $link;

    public function __construct(Link $link)
    {
        $this->link = $link;
    }

    public function execute(array $params): array
    {
        $builder = $this->link
            ->withTrashed()
            ->with(['rules', 'tags', 'pixels', 'groups', 'domain']);

        if ($groupId = Arr::get($params, 'groupId')) {
            // get only links that either belong to specified group or belong to any group besides it
            $operator = Str::contains($groupId, '!') ? 'doesntHave' : 'has';
            $groupId = str_replace('!', '', $groupId);
            $builder->whereHas(
                'groups',
                function (Builder $builder) use ($groupId) {
                    $builder->where('link_group_id', $groupId);
                },
                $operator,
            );
        }

        $dataSource = new Datasource($builder, $params);

        return $dataSource->paginate()->toArray();
    }
}
