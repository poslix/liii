<?php

namespace App\Http\Controllers;

use App\Biolink;
use App\BiolinkWidget;
use App\Link;
use Common\Core\BaseController;
use DB;

class BiolinkContentOrderController extends BaseController
{
    public function changeOrder(Biolink $biolink)
    {
        $this->authorize('update', $biolink);

        $this->validate(request(), [
            'order' => 'array|min:1',
            'order.*.id' => 'required|integer',
            'order.*.model_type' => 'required|string',
        ]);

        $widgetQuery = '';
        $linkQuery = '';
        foreach (request()->get('order') as $position => $value) {
            $position++;
            $id = $value['id'];
            if ($value['model_type'] === Link::MODEL_TYPE) {
                $linkQuery .= " when link_id=$id then $position";
            } else {
                $widgetQuery .= " when id=$id then $position";
            }
        }

        if ($linkQuery) {
            $linkIds = collect(request('order'))
                ->where('model_type', Link::MODEL_TYPE)
                ->pluck('id');
            DB::table('link_group_link')
                ->where('link_group_id', $biolink->id)
                ->whereIn('link_id', $linkIds)
                ->update(['position' => DB::raw("(case $linkQuery end)")]);
        }

        if ($widgetQuery) {
            $widgetIds = collect(request('order'))
                ->where('model_type', BiolinkWidget::MODEL_TYPE)
                ->pluck('id');
            BiolinkWidget::where('biolink_id', $biolink->id)
                ->whereIn('id', $widgetIds)
                ->update(['position' => DB::raw("(case $widgetQuery end)")]);
        }

        return $this->success();
    }
}
