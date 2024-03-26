<?php

namespace App\Models;

use App\Content\Translation\TranslatableModelInterface;
use Illuminate\Database\Eloquent\Model;

class Project extends Model implements TranslatableModelInterface
{
    protected $appends = [
        'thumbnail_url',
    ];

    protected $guarded = ['id'];

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTranslatableType(): string
    {
        return 'project';
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getThumbnailUrlAttribute($value)
    {
        return asset("assets/images/{$this->attributes['thumbnail']}");
    }
}
