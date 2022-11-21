<?php

namespace App\Actions\Biolink;

use App\Actions\Link\LinkeablePublicPolicy;
use App\Biolink;
use App\BiolinkWidget;
use App\Link;

class LoadBiolinkContent
{
    public function execute(Biolink $biolink, array $params = []): Biolink
    {
        if (isset($params['with'])) {
            $biolink->load(explode(',', $params['with']));
        }

        $links = $biolink
            ->links()
            ->with(['rules', 'tags', 'pixels', 'domain'])
            ->get()
            ->map(function (Link $link) {
                if (
                    LinkeablePublicPolicy::linkeableExpired($link) ||
                    LinkeablePublicPolicy::linkeableWillActivateLater($link)
                ) {
                    $link->active = false;
                    $link->active_locked = true;
                }

                $link->model_type = Link::MODEL_TYPE;
                $link->position = $link->pivot->position;
                $link->animation = $link->pivot->animation;
                $link->leap_until = $link->pivot->leap_until;
                unset($link->pivot);
                return $link;
            });

        $widgets = $biolink
            ->widgets()
            ->get()
            ->map(function (BiolinkWidget $widget) {
                $widget->model_type = BiolinkWidget::MODEL_TYPE;
                return $widget;
            });

        $biolink->content = $links
            ->concat($widgets)
            ->sortBy('position')
            ->values();

        return $biolink;
    }
}
