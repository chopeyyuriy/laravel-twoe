<?php

namespace App\Http\Controllers\Admin;

use App\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TagsController extends Controller
{
    public function index()
    {
        $all_tags = Tag::all();
        return view('admin.tags.index', ['all_tags' => $all_tags]);
    }

    public function create()
    {
        return view('admin.tags.tags_create');
    }

    public function store(Request $request)
    {
        $tag = new Tag();
        $tag->name = $request->name;
        if ($request->popular) {
            $tag->popular = $request->popular;
        } else {
            $tag->popular = 0;
        }
        $tag->save();
        return redirect('/admin/tags')->with('success', 'Тег создан !');
    }

    public function show($id)
    {
        $tag = Tag::where('id', $id)->get();

        return response()->json([
            'tags' => $tag
        ]);
    }

    public function edit($id)
    {
        $tag = Tag::findOrFail($id);
        return view('admin.tags.tags_edit',compact('tag'));
    }

    public function update(Request $request, $id)
    {
        $edit_tag = Tag::findOrFail($id);
        $edit_tag->name = $request->name;
        if ($request->popular == 1){
            $edit_tag->popular = 1;
        } else {
            $edit_tag->popular = 0;
        }
        $edit_tag->save();
        return redirect('/admin/tags')->with('success', 'Тег изменена !');
    }

    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();
        return redirect('/admin/tags')->with('success', 'Тег удален !');
    }
}
