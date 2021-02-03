<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pages extends Model
{
    protected $fillable = [
        'url',
        'title',
        'content',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

}
