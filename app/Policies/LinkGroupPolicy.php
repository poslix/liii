<?php

namespace App\Policies;

use App\LinkGroup;
use App\User;
use Illuminate\Database\Eloquent\Model;

class LinkGroupPolicy extends WorkspacedResourcePolicy
{
    protected $resource = LinkGroup::class;

    public function show(User $currentUser, Model $resource): bool
    {
        $workspace = $this->getWorkspace();
        return (!$workspace && $resource->active) ||
            parent::show($currentUser, $resource);
    }
}
