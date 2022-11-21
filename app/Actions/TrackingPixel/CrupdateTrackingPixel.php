<?php

namespace App\Actions\TrackingPixel;

use App\TrackingPixel;
use Auth;
use Common\Workspaces\ActiveWorkspace;
use Illuminate\Support\Arr;

class CrupdateTrackingPixel
{
    /**
     * @var TrackingPixel
     */
    private $pixel;

    public function __construct(TrackingPixel $trackingPixel)
    {
        $this->pixel = $trackingPixel;
    }

    public function execute(
        array $data,
        TrackingPixel $pixel = null
    ): TrackingPixel {
        if (!$pixel) {
            $pixel = $this->pixel->newInstance([
                'user_id' => Auth::id(),
            ]);
        }

        $attributes = [
            'name' => $data['name'],
            'type' => $data['type'],
            'pixel_id' => $data['pixel_id'],
            'head_code' => Arr::get($data, 'head_code'),
            'body_code' => Arr::get($data, 'body_code'),
            'workspace_id' => app(ActiveWorkspace::class)->id,
        ];

        $pixel->fill($attributes)->save();

        return $pixel;
    }
}
