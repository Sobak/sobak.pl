<?php

namespace App\Models;

use App\Content\Translation\TranslatableModelInterface;
use Illuminate\Database\Eloquent\Model;

class Page extends Model implements TranslatableModelInterface
{
    protected $guarded = ['id'];

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTranslatableType(): string
    {
        return 'page';
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
