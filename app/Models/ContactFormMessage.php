<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactFormMessage extends Model
{
    const UPDATED_AT = null;

    protected $connection = 'persistent';

    protected $guarded = ['id'];
}
