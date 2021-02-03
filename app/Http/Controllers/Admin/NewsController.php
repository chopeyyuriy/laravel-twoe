<?php

namespace App\Http\Controllers\Admin;

use App\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $all_news = News::all();
        return view('admin.news.index', ['all_news' => $all_news]);
    }

    public function create()
    {
        return view('admin.news.news_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required',
            'text_news'=> 'required',
        ]);
        $news = new News([
            'title' => $request->title,
            'text_news'=> $request->text_news,
            'url'=> Str::slug($request->title, '-'),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'status' => 'active'
        ]);
        if (isset($request->image)) {
            $image_name = $request->image->getClientOriginalName();
            Storage::disk('news')->putFileAs('/', $request->image, $image_name);
            $news->image = $image_name;
        }

        $news->save();
        return redirect('/admin/news')->with('success', 'Новость создана !');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.news_edit', compact('news'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'=>'required',
            'text_news'=> 'required',
        ]);
        $edit_news = News::findOrFail($id);
        $edit_news->title = $request->title;
        $edit_news->text_news = $request->text_news;
        $edit_news->url = Str::slug($request->title, '-');
        $edit_news->meta_title = $request->meta_title;
        $edit_news->meta_description = $request->meta_description;
        $edit_news->meta_keywords = $request->meta_keywords;
        if (isset($request->image)) {
            $image_name = $request->image->getClientOriginalName();
            Storage::disk('news')->putFileAs('/', $request->image, $image_name);
            $edit_news->image = $image_name;
        }
        $edit_news->save();
        return redirect('/admin/news')->with('success', 'Новость изменена !');
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();
        return redirect('/admin/news')->with('success', 'Новость удалена !');
    }



}
