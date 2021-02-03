<?php

namespace App\Http\Controllers\Admin;

use App\Pages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PagesController extends Controller
{
    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        $page = Pages::where('id', $id)->first();
        return view('admin.pages.page_edit', compact('page'));
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'title'=>'required',
            'page_content'=> 'required',
            'meta_title'=> 'required',
            'meta_description'=> 'required',
            'meta_keywords'=> 'required',
        ]);
        $edit_page = Pages::findOrFail($id);
        $edit_page->title = $request->title;
        $edit_page->content = $request->page_content;
        $edit_page->meta_title = $request->meta_title;
        $edit_page->meta_description = $request->meta_description;
        $edit_page->meta_keywords = $request->meta_keywords;
        $edit_page->save();

        return redirect()->route('pages.show', ['id' =>$id])->with('success', "Страница \"$request->title\" изменена !");

    }

    public function destroy($id)
    {
        //
    }
}
