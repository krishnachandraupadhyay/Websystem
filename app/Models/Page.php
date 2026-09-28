<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'page_name',
        'title',
        'slug',
        'description',
        'status',
        'order',
    ];
}
