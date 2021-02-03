<?php

namespace App\Services;

class News
{
    public function getNews()
    {
        $news = \App\News::orderBy('created_at', 'desc')->take(5)->get();
        return ['news' => $news];
    }
}