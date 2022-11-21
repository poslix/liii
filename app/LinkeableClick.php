<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LinkClick
 *
 * @property int $id
 * @property int $link_id
 * @property string|null $ip
 * @property string|null $referrer
 * @property string|null $platform
 * @property string|null $device
 * @property string|null $browser
 * @property string|null $location
 * @property int $crawler
 * @property \Carbon\Carbon $created_at
 * @mixin \Eloquent
 * @property string|null $city
 * @property string|null $state
 * @method static \Database\Factories\LinkeableClickFactory factory(...$parameters)
 */
class LinkeableClick extends Model
{
    use HasFactory;

    protected $table = 'link_clicks';

    const UPDATED_AT = null;

    protected $guarded = ['id'];

    public function linkeable()
    {
        return $this->morphTo('linkeable');
    }
}
