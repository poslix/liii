<?php

namespace App;

use Carbon\Carbon;
use Common\Search\Searchable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin Eloquent
 * @property string $name
 * @property string $position
 * @property string $message
 * @property string|null $label
 * @property string|null $btn_link
 * @property string|null $btn_text
 * @property string $colors
 * @property int|null $workspace_id
 * @property string $theme
 * @property string|null $image
 * @property-read \App\User $user
 * @method static Builder|LinkOverlay basicSearch(string $query)
 * @method static Builder|LinkOverlay newModelQuery()
 * @method static Builder|LinkOverlay newQuery()
 * @method static Builder|LinkOverlay query()
 * @method static Builder|LinkOverlay whereBtnLink($value)
 * @method static Builder|LinkOverlay whereBtnText($value)
 * @method static Builder|LinkOverlay whereColors($value)
 * @method static Builder|LinkOverlay whereCreatedAt($value)
 * @method static Builder|LinkOverlay whereId($value)
 * @method static Builder|LinkOverlay whereImage($value)
 * @method static Builder|LinkOverlay whereLabel($value)
 * @method static Builder|LinkOverlay whereMessage($value)
 * @method static Builder|LinkOverlay whereName($value)
 * @method static Builder|LinkOverlay wherePosition($value)
 * @method static Builder|LinkOverlay whereTheme($value)
 * @method static Builder|LinkOverlay whereUpdatedAt($value)
 * @method static Builder|LinkOverlay whereUserId($value)
 * @method static Builder|LinkOverlay whereWorkspaceId($value)
 */
class LinkOverlay extends Model
{
    use Searchable;

    protected $guarded = ['id'];
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
    ];

    const MODEL_TYPE = 'linkOverlay';

    public function setColorsAttribute($value)
    {
        if ($value && is_array($value)) {
            $this->attributes['colors'] = json_encode($value);
        }
    }

    public function getColorsAttribute($value)
    {
        if ($value && is_string($value)) {
            return json_decode($value, true);
        } else {
            return [];
        }
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'message' => $this->message,
            'label' => $this->label,
            'btn_link' => $this->btn_link,
            'btn_text' => $this->btn_text,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at->timestamp ?? '_null',
            'updated_at' => $this->updated_at->timestamp ?? '_null',
            'theme' => $this->theme,
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
            'theme',
            'workspace_id',
        ];
    }
}
