<?php

namespace App;

use Arr;
use Common\Domains\CustomDomain;
use Common\Search\Searchable;
use Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\LinkGroup
 *
 * @property int $id
 * @property boolean $rotator
 * @property string $name
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $active
 * @property string|null $description
 * @property string $hash
 * @property int|null $workspace_id
 * @property int|null $domain_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Link[] $links
 * @property-read int|null $links_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Link[] $randomLink
 * @property-read int|null $random_link_count
 * @property-read \App\User $user
 * @mixin \Eloquent
 */
class LinkGroup extends Model
{
    const MODEL_TYPE = 'linkGroup';

    use Searchable, HasFactory, SoftDeletes, HasShortUrlAttribute;

    protected $guarded = ['id'];
    protected $appends = ['short_url', 'has_password', 'model_type'];
    protected $dates = ['expires_at', 'clicked_at', 'activates_at'];
    protected $hidden = ['type'];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'active' => 'boolean',
        'rotator' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope('groupType', function (Builder $builder) {
            $builder->where((new static())->qualifyColumn('type'), static::MODEL_TYPE);
        });

        static::creating(function (Model $builder) {
            $builder->type = static::MODEL_TYPE;
        });
    }

    public function links(): BelongsToMany {
        return $this->belongsToMany(Link::class, 'link_group_link', 'link_group_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function randomLink() {
        return $this->belongsToMany(Link::class, 'link_group_link')
            ->inRandomOrder()
            ->limit(1);
    }

    public function rules(): MorphMany
    {
        return $this->morphMany(LinkeableRule::class, 'linkeable');
    }

    public function clicks(): MorphMany
    {
        return $this->morphMany(LinkeableClick::class, 'linkeable');
    }

    public function pixels(): BelongsToMany
    {
        return $this->morphToMany(
            TrackingPixel::class,
            'linkeable',
            'link_tracking_pixel',
        );
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(CustomDomain::class, 'domain_id')->select([
            'id',
            'host',
        ]);
    }

    public function getHasPasswordAttribute()
    {
        return !!Arr::get($this->attributes, 'password');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value ? Hash::make($value) : null;
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at->timestamp ?? '_null',
            'updated_at' => $this->updated_at->timestamp ?? '_null',
            'active' => $this->active,
            'description' => $this->description,
            'workspace_id' => $this->workspace_id ?? '_null',
            'rotator' => $this->rotator,
            'type' => static::MODEL_TYPE,
        ];
    }

    public static function filterableFields(): array
    {
        return [
            'id',
            'user_id',
            'created_at',
            'updated_at',
            'active',
            'workspace_id',
            'rotator',
            'type',
        ];
    }

    public static function getModelTypeAttribute(): string
    {
        return static::MODEL_TYPE;
    }
}
