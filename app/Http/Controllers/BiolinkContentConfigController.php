<?php

namespace App\Http\Controllers;

use App\Biolink;
use App\Link;
use Common\Core\BaseController;

class BiolinkContentConfigController extends BaseController
{
    public function update(Biolink $biolink)
    {
        $this->authorize('update', $biolink);

        $data = $this->validate(request(), [
            'active' => 'boolean',
            'image' => 'nullable|string',
            'animation' => 'nullable|string',
            'leap_until' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'activates_at' => 'nullable|date',
            'item_id' => 'required|int',
            'item_model_type' => 'required|string',
        ]);
        $isLink = $data['item_model_type'] === Link::MODEL_TYPE;

        if ($isLink) {
            $this->setPivotAttrs($biolink, $data);
        }

        $linkProps = ['image', 'expires_at', 'activates_at'];
        foreach ($linkProps as $prop) {
            if (array_key_exists($prop, $data)) {
                $biolink
                    ->links()
                    ->where('links.id', $data['item_id'])
                    ->update([$prop => $data[$prop]]);
            }
        }

        if (array_key_exists('active', $data)) {
            $relation = $isLink ? $biolink->links() : $biolink->widgets();
            $relation
                ->where($relation->qualifyColumn('id'), $data['item_id'])
                ->update(['active' => $data['active']]);
        }

        return $this->success();
    }

    private function setPivotAttrs(Biolink $biolink, array $data)
    {
        $pivotAttrs = [];

        if (array_key_exists('animation', $data)) {
            $pivotAttrs['animation'] = $data['animation'];
        }
        if (array_key_exists('leap_until', $data)) {
            // clear all other leap links for this biolink
            $biolink
                ->links()
                ->whereNotNull('leap_until')
                ->update(['leap_until' => null]);
            $pivotAttrs['leap_until'] = $data['leap_until'];
        }

        if (!empty($pivotAttrs)) {
            $biolink
                ->links()
                ->updateExistingPivot($data['item_id'], $pivotAttrs);
        }
    }
}
