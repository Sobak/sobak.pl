<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedirectLog extends Model
{
    const UPDATED_AT = null;

    protected $connection = 'permanent';

    protected $guarded = ['id'];
}
