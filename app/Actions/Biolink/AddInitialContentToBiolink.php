<?php

namespace App\Actions\Biolink;

use App\BiolinkWidget;
use App\User;
use Auth;

class AddInitialContentToBiolink
{

    public function execute(int $biolinkId, User $user = null)
    {
        $widgets = [
            [
                'biolink_id' => $biolinkId,
                'type' => 'image',
                'position' => 1,
                'config' => json_encode([
                    'url' => 'profile-pic.svg',
                    'type' => 'avatar',
                ]),
            ],
            [
                'biolink_id' => $biolinkId,
                'type' => 'text',
                'position' => 2,
                'config' => json_encode([
                    'title' => '@' . ($user ?? Auth::user())->display_name,
                ]),
            ],
            [
                'biolink_id' => $biolinkId,
                'type' => 'socials',
                'position' => 99,
                'config' => json_encode([
                    'facebook' => 'https://facebook.com/username',
                    'instagram' => '#instagram-handle',
                    'twitter' => '#twitter-handle',
                ]),
            ],
        ];

        BiolinkWidget::insert($widgets);
    }
}
