<?php

namespace App;

use Common\Pages\CustomPage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LinkPage
 *
 * @property int $id
 * @property string|null $title
 * @property string $body
 * @property string $slug
 * @property string|null $meta
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $user_id
 * @property int|null $workspace_id
 * @property bool $hide_nav
 * @property-read \Illuminate\Database\Eloquent\Collection|\Common\Tags\Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read \App\User|null $user
 * @mixin \Eloquent
 */
class LinkPage extends CustomPage
{
    public $table = 'custom_pages';
    const PAGE_TYPE = 'link_page';

    protected static function booted()
    {
        static::addGlobalScope('linkPage', function (Builder $builder) {
            $builder->where('type', self::PAGE_TYPE);
        });

        static::creating(function (Model $builder) {
            $builder->type = self::PAGE_TYPE;
        });
    }
}
