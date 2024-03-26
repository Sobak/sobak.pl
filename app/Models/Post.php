<?php

namespace App\Models;

use App\Content\Translation\TranslatableModelInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements TranslatableModelInterface
{
    protected $guarded = ['id'];

    public static function getTranslatableType(): string
    {
        return 'post';
    }

    public static function getAllSlugs(): array
    {
        return Post::pluck('slug')->toArray();
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('published', function (Builder $builder) {
            if (config('content.show_scheduled') === false) {
                $builder->where('created_at', '<=', Carbon::now()->toDateTimeString());
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'slug', 'project');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
