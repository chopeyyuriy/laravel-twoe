<?php

namespace App\Http\Controllers\Admin;

use App\Notifications;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();
        $all_notifications = Notifications::where('user_id', $user_id)->where('status', 1)->get();
        return view('admin.notifications.index', ['all_notifications' => $all_notifications]);
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
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
