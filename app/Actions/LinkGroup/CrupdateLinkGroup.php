<?php

namespace App\Actions\LinkGroup;

use App\Biolink;
use App\LinkGroup;
use Arr;
use Auth;
use Common\Workspaces\ActiveWorkspace;

class CrupdateLinkGroup
{
    /**
     * @param LinkGroup|Biolink $initialGroup
     * @param array $data
     */
    public function execute($initialGroup, array $data)
    {
        if (!$initialGroup->exists) {
            $group = $initialGroup->create([
                'user_id' => Auth::id(),
                'hash' => $data['hash'],
            ]);
        } else {
            $group = $initialGroup;
        }

        $attributes = [
            'name' => $data['name'],
            'description' => $data['description'],
            'expires_at' => $data['expires_at'] ?? null,
            'activates_at' => $data['activates_at'] ?? null,
            'active' => $data['active'] ?? true,
            'hash' => $data['hash'],
            'utm' => $data['utm'] ?? null,
            'rotator' => $data['rotator'] ?? false,
            'domain_id' => $data['domain_id'] ?? null, // can be 0
            'workspace_id' => app(ActiveWorkspace::class)->id,
        ];

        // restore group if user has removed expires_at date from expired link
        if (is_null($attributes['expires_at'])) {
            $attributes['deleted_at'] = null;
        }

        // make sure not to clear password if it was not changed
        if (Arr::has($data, 'password')) {
            $attributes['password'] = $data['password'] ?: null;
        }

        $group->fill($attributes)->save();

        if (isset($data['rules'])) {
            $group->rules()->delete();
            $rules = $group->rules()->createMany(Arr::get($data, 'rules'));
            $group->setRelation('rules', $rules);
        }

        if (Arr::get($data, 'exp_clicks_rule.key')) {
            $group
                ->rules()
                ->updateOrCreate(
                    ['type' => 'exp_clicks'],
                    $data['exp_clicks_rule'],
                );
        }

        if (isset($data['pixels'])) {
            $group->pixels()->sync(Arr::get($data, 'pixels'));
        }

        return $group;
    }
}
