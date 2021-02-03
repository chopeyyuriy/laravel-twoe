<?php

namespace App\Http\Controllers;

use App\News;
use App\Pages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PagesController extends Controller
{
    public function getPageContent($url)
    {
        if ($url) {
            $page = Pages::where('url', $url)->first();
            if ($page) {
                return view('page', compact('page'));
            } else {
                return redirect()->route('index');
            }
        } else {
            return redirect()->route('index');
        }

    }

    public function blog()
    {
        $title = 'Блог';
        return view('page', ['title' => $title]);
    }
}
