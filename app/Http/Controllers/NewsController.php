<?php

namespace App\Http\Controllers;

use App\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    public function index()
    {
        $newss = News::all();
        return view('news', ['newss' => $newss]);
    }

    public function show($url)
    {
        $news = News::where('url', $url)->first();
        return view('news_show', ['news' => $news]);
    }
}
