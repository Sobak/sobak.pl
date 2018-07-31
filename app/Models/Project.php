<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $appends = [
        'thumbnail_url',
    ];

    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getThumbnailUrlAttribute($value)
    {
        return asset("assets/images/{$this->attributes['thumbnail']}");
    }
}
