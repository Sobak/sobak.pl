<?php

namespace App\Models;

use App\Content\Translation\TranslatableModelInterface;
use Illuminate\Database\Eloquent\Model;

class Page extends Model implements TranslatableModelInterface
{
    protected $guarded = ['id'];

    public static function getTranslatableType(): string
    {
        return 'page';
    }

    public static function getAllSlugs(): array
    {
        return Page::pluck('slug')->toArray();
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
