<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * @property BiolinkAppearance $appearance
 * @property Collection $content
 * @property string $long_url
 */
class Biolink extends LinkGroup
{
    use HasFactory;

    public $table = 'link_groups';
    const MODEL_TYPE = 'biolink';

    public function links(): BelongsToMany
    {
        return $this->belongsToMany(
            Link::class,
            'link_group_link',
            'link_group_id',
        )
            ->using(BiolinkPivot::class)
            ->withPivot(['position', 'animation', 'leap_until']);
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(BiolinkWidget::class);
    }

    public function appearance(): HasOne
    {
        return $this->hasOne(BiolinkAppearance::class);
    }
}
