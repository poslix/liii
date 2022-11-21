<?php

namespace App\Actions\Overlay;

use Auth;
use App\Link;
use App\LinkOverlay;
use Common\Workspaces\ActiveWorkspace;

class CrupdateLinkOverlay
{
    /**
     * @var Link
     */
    private $overlay;

    public function __construct(LinkOverlay $overlay)
    {
        $this->overlay = $overlay;
    }

    public function execute(
        array $data,
        LinkOverlay $overlay = null
    ): LinkOverlay {
        if (!$overlay) {
            $overlay = $this->overlay->newInstance(['user_id' => Auth::id()]);
        }

        $attributes = [
            'name' => $data['name'],
            'position' => $data['position'],
            'theme' => $data['theme'] ?? 'default',
            'message' => $data['message'],
            'label' => $data['label'],
            'btn_link' => $data['btn_link'],
            'btn_text' => $data['btn_text'],
            'colors' => $data['colors'],
            'workspace_id' => app(ActiveWorkspace::class)->id,
        ];

        $overlay->fill($attributes)->save();

        return $overlay;
    }
}
