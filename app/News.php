<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
        'text_news',
        'url',
        'image',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function getNewsImage()
    {
        return '/app/news/'.$this->image;
    }

}
