<?php

namespace App\Actions;

use App\LinkDomain;
use App\LinkGroup;
use App\LinkOverlay;
use App\LinkPage;
use App\TrackingPixel;
use Arr;
use Auth;
use Common\Auth\Permissions\Permission;
use Common\Core\Values\ValueLists;
use Common\Workspaces\ActiveWorkspace;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Str;
use const App\Providers\WORKSPACED_RESOURCES;

class AppValueLists extends ValueLists
{
    public function overlays($params = [])
    {
        $userId = $params['userId'] ?? Auth::id();
        app(Gate::class)->authorize('index', [LinkOverlay::class, $userId]);

        return app(LinkOverlay::class)
            ->select(['id', 'name'])
            ->where('user_id', $userId)
            ->limit(30)
            ->get();
    }

    public function pixels($params = [])
    {
        $userId = $params['userId'] ?? Auth::id();
        app(Gate::class)->authorize('index', [TrackingPixel::class, $userId]);

        return app(TrackingPixel::class)
            ->select(['id', 'name'])
            ->where('user_id', $userId)
            ->limit(30)
            ->get();
    }

    public function groups($params = [])
    {
        $userId = $params['userId'] ?? Auth::id();
        app(Gate::class)->authorize('index', [LinkGroup::class, $userId]);

        return app(LinkGroup::class)
            ->select(['id', 'name'])
            ->where('user_id', $userId)
            ->limit(30)
            ->get();
    }

    public function domains($params): Collection
    {
        $userId = $params['userId'] ?? Auth::id();
        $query = app(LinkDomain::class)
            ->select(['host', 'id'])
            ->where('user_id', $userId)
            ->orWhere('global', true);
        if ($workspaceId = app(ActiveWorkspace::class)->id) {
            $query->orWhere('workspace_id', $workspaceId);
        }

        return $query->limit(30)->get();
    }

    public function pages($params = [])
    {
        if (!isset($params['userId'])) {
            app(Gate::class)->authorize('index', LinkPage::class);
        }

        $query = app(LinkPage::class)->select(['id', 'title']);

        if ($userId = Arr::get($params, 'userId')) {
            $query->where('user_id', $userId);
        }

        return $query->limit(30)->get();
    }

    public function workspacePermissions($params = [])
    {
        $filters = array_map(function ($resource) {
            if ($resource === LinkDomain::class) {
                return 'custom_domains';
            } else {
                return Str::snake(Str::pluralStudly(class_basename($resource)));
            }
        }, WORKSPACED_RESOURCES);

        return app(Permission::class)
            ->where('type', 'workspace')
            ->orWhere(function (Builder $builder) use ($filters) {
                $builder->where('type', 'sitewide')->whereIn('group', $filters);
            })
            // don't return restrictions for workspace permissions so they
            // are not show when creating workspace role from admin area
            ->get([
                'id',
                'name',
                'display_name',
                'description',
                'group',
                'type',
            ])
            ->map(function (Permission $permission) {
                $permission->description = str_replace(
                    'ALL',
                    'all workspace',
                    $permission->description,
                );
                $permission->description = str_replace(
                    'creating new',
                    'creating new workspace',
                    $permission->description,
                );
                return $permission;
            });
    }
}
