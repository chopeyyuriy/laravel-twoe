<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function index()
    {
        $all_users = User::all();
        return view('admin.users.index', ['all_users' => $all_users]);
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
        $users = User::where('id', $id)->get();
        return response()->json([
            'users' => $users
        ]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect('/admin/users')->with('success', 'Пользователь удаленный !');
    }
}
