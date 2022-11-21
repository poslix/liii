<?php

namespace App\Http\Controllers;

use App\Biolink;
use App\BiolinkWidget;
use Common\Core\BaseController;

class BiolinkWidgetsController extends BaseController
{
    public function store(Biolink $biolink)
    {
        $this->authorize('update', $biolink);

        $payload = $this->validate(request(), [
            'type' => 'required|string',
            'position' => 'required|int',
            'config' => 'required|array',
        ]);

        $widget = $biolink->widgets()->create($payload);

        return $this->success(['widget' => $widget]);
    }

    public function update(Biolink $biolink, BiolinkWidget $widget)
    {
        $this->authorize('update', $biolink);

        $payload = $this->validate(request(), [
            'type' => 'string',
            'config' => 'array',
        ]);

        $widget->update($payload);

        return $this->success(['widget' => $widget]);
    }

    public function destroy(Biolink $biolink, string $widgetIds)
    {
        $widgetIds = explode(',', $widgetIds);
        $this->authorize('update', $biolink);

        $biolink->widgets()->whereIn('id', $widgetIds)->delete();

        return $this->success();
    }
}
