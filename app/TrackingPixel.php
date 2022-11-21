<?php

namespace App;

use Carbon\Carbon;
use Common\Search\Searchable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\TrackingPixel
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin Eloquent
 * @property string $name
 * @property string $type
 * @property string|null $pixel_id
 * @property string|null $head_code
 * @property string|null $body_code
 * @property int|null $workspace_id
 * @property-read \App\User $user
 * @method static Builder|TrackingPixel basicSearch(string $query)
 * @method static Builder|TrackingPixel newModelQuery()
 * @method static Builder|TrackingPixel newQuery()
 * @method static Builder|TrackingPixel query()
 * @method static Builder|TrackingPixel whereBodyCode($value)
 * @method static Builder|TrackingPixel whereCreatedAt($value)
 * @method static Builder|TrackingPixel whereHeadCode($value)
 * @method static Builder|TrackingPixel whereId($value)
 * @method static Builder|TrackingPixel whereName($value)
 * @method static Builder|TrackingPixel wherePixelId($value)
 * @method static Builder|TrackingPixel whereType($value)
 * @method static Builder|TrackingPixel whereUpdatedAt($value)
 * @method static Builder|TrackingPixel whereUserId($value)
 * @method static Builder|TrackingPixel whereWorkspaceId($value)
 */
class TrackingPixel extends Model
{
    use Searchable;

    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
    ];

    const MODEL_TYPE = 'trackingPixel';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at->timestamp ?? '_null',
            'updated_at' => $this->updated_at->timestamp ?? '_null',
            'workspace_id' => $this->workspace_id ?? '_null',
        ];
    }

    public static function filterableFields(): array
    {
        return [
            'id',
            'user_id',
            'created_at',
            'updated_at',
            'type',
            'workspace_id',
        ];
    }
}
