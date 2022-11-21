<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiolinkWidget extends Model
{
    use HasFactory;

    const MODEL_TYPE = 'biolinkWidget';

    protected $guarded = ['id'];

    protected static function booted()
    {
        // sticky socials widget to bottom
        static::creating(function (Model $builder) {
            if ($builder->type === 'socials') {
                $builder->position = 99;
            }
        });
    }

    public function getConfigAttribute($value)
    {
        return json_decode($value, true) ?: [];
    }

    public function setConfigAttribute($value)
    {
        if (is_string($value)) return;
        $this->attributes['config'] = json_encode($value);
    }
}
