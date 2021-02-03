<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CoursesController extends Controller
{

    public function index()
    {
        $courses = Courses::all();
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.courses_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'curs' => 'required',
            'interest' => 'required',
        ]);
        Courses::create($request->all());
        return redirect('/admin/courses')->with('success', 'Курс создан !');
    }

    public function show($id)
    {
        $curses = Courses::where('id', $id)->get();
        return response()->json(['courses' => $curses]);
    }

    public function edit($id)
    {
        // $courses = Courses::findOrFail($id);
        // return view('admin.courses.courses_edit', compact('courses'));
    }

    public function update(Request $request, $id, Courses $courses)
    {
        $request->validate([
            'title' => 'required',
            'curs' => 'required',
            'interest' => 'required',
        ]);
        $courses = Courses::findOrFail($id);
        $courses->update($request->all());
        return redirect('/admin/courses')->with('success', 'Курс изменен !');
    }

    public function destroy($id)
    {
        $courses = Courses::findOrFail($id);
        $courses->delete();
        return redirect('/admin/courses')->with('success', 'Курс удален !');
    }
}
