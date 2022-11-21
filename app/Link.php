<?php

namespace App;

use Arr;
use Carbon\Carbon;
use Common\Domains\CustomDomain;
use Common\Pages\CustomPage;
use Common\Search\Searchable;
use Common\Tags\Tag;
use Database\Factories\LinkFactory;
use Eloquent;
use Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Link
 *
 * @property int $id
 * @property string $hash
 * @property string $alias
 * @property string $long_url
 * @property string|null $password
 * @property Carbon|null $expires_at
 * @property Carbon|null $activates_at
 * @property int $expiration_clicks
 * @property string|null $description
 * @property string|LinkOverlay|null $type
 * @property int|null $type_id
 * @property int $user_id
 * @property integer $domain_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|LinkeableClick[] $clicks
 * @property-read string $short_url
 * @property-read Collection|LinkeableRule[] $rules
 * @property-read Collection|Tag[] $tags
 * @property-read User $user
 * @property-read LinkOverlay|null $custom_page
 * @property-read TrackingPixel[]|Collection $pixels
 * @property-read CustomDomain $domain
 * @mixin Eloquent
 * @property string|null $name
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $clicked_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $workspace_id
 * @property string|null $thumbnail
 * @property string|null $image
 * @property-read int|null $clicks_count
 * @property string|null $utm
 * @property-read mixed $has_password
 * @property-read Collection|LinkGroup[] $groups
 * @property-read int|null $groups_count
 * @property-read int|null $pixels_count
 * @property-read int|null $rules_count
 * @property-read int|null $tags_count
 * @method static Builder|Link basicSearch(string $query)
 * @method static LinkFactory factory(...$parameters)
 * @method static \Illuminate\Database\Query\Builder|Link withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Link withoutTrashed()
 * @method static Builder|Link matches(array $columns, string $value)
 * @method static Builder|Link newModelQuery()
 * @method static Builder|Link newQuery()
 * @method static \Illuminate\Database\Query\Builder|Link onlyTrashed()
 * @method static Builder|Link query()
 * @method static Builder|Link whereHash(string $hash)
 */
class Link extends Model
{
    use SoftDeletes, Searchable, HasFactory, HasShortUrlAttribute;

    const MODEL_TYPE = 'link';

    protected $guarded = ['id'];
    protected $hidden = ['password'];
    protected $appends = ['short_url', 'has_password', 'model_type'];
    protected $attributes = ['type' => 'default'];
    protected $dates = ['expires_at', 'clicked_at', 'activates_at'];

    protected $casts = [
        'id' => 'integer',
        'domain_id' => 'integer',
        'user_id' => 'integer',
        'active' => 'boolean',
        'has_password' => 'boolean',
    ];

    public function rules(): MorphMany
    {
        return $this->morphMany(LinkeableRule::class, 'linkeable');
    }

    public function clicks(): MorphMany
    {
        return $this->morphMany(LinkeableClick::class, 'linkeable');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function groups(): BelongsToMany
    {
        // will get scoped by "type" column on "link_groups" table
        return $this->belongsToMany(LinkGroup::class, 'link_group_link', null, 'link_group_id');
    }

    public function biolinks(): BelongsToMany
    {
        // will get scoped by "type" column on "link_groups" table
        return $this->belongsToMany(Biolink::class, 'link_group_link', null, 'link_group_id');
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

    public function customPage(): BelongsTo
    {
        return $this->belongsTo(CustomPage::class, 'type_id');
    }

    public function overlay(): BelongsTo
    {
        return $this->belongsTo(LinkOverlay::class, 'type_id');
    }

    public function getHasPasswordAttribute()
    {
        return !!Arr::get($this->attributes, 'password');
    }

    public function getLongUrlAttribute($value)
    {
        return parse_url($value, PHP_URL_SCHEME) === null
            ? "https://$value"
            : $value;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value ? Hash::make($value) : null;
    }

    public function scopeWhereHash(Builder $builder, string $hash): Builder
    {
        return $this->where('hash', $hash)->orWhere('alias', $hash);
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'alias' => $this->alias,
            'long_url' => $this->long_url,
            'description' => $this->description,
            'hash' => $this->hash,
            'type' => $this->type,
            'active' => $this->active,
            'groups' => $this->groups->pluck('id'),
            'biolinks' => $this->biolinks->pluck('id'),
            'user_id' => $this->user_id,
            'created_at' => $this->created_at->timestamp ?? '_null',
            'updated_at' => $this->updated_at->timestamp ?? '_null',
            'expires_at' => $this->expires_at->timestamp ?? '_null',
            'password' => $this->password,
            'clicks_count' => $this->clicks_count,
            'tags' => $this->tags->map(function (Tag $tag) {
                return $tag->getSearchableValues();
            }),
            'workspace_id' => $this->workspace_id ?? '_null',
        ];
    }

    protected function makeAllSearchableUsing($query)
    {
        return $query->with(['groups', 'tags', 'biolinks']);
    }

    public static function filterableFields(): array
    {
        return [
            'id',
            'hash',
            'type',
            'active',
            'groups',
            'user_id',
            'created_at',
            'updated_at',
            'expires_at',
            'password',
            'clicks_count',
            'workspace_id',
        ];
    }

    public static function getModelTypeAttribute(): string
    {
        return Link::MODEL_TYPE;
    }
}
