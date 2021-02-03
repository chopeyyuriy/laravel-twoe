<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserValid;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;


class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $auth_user = Auth::user();
        return view('account_settings', ['auth_user' => $auth_user]);

    }

    public function editUser(UserValid $request)
    {
        $auth_user = Auth::user();
        $auth_user->first_name = $request->first_name;
        $auth_user->last_name = $request->last_name;
        $auth_user->login = $request->new_login;
        $auth_user->telegram = $request->telegram;

        if (isset($request->email)) {
            if ($request->email == $request->email_confirmation) {
                if ($request->email != $auth_user->email) {
                    $auth_user->email = $request->email;
                }
            }
        }
        if (isset($request->password)) {
            if ($request->password == $request->password_confirmation) {
                if ($request->password != $auth_user->password) {
                    $hesh_pass = Hash::make($request->password);
                    $auth_user->password = $hesh_pass;
                }
            }
        }

        if (isset($request->newAvatar)) {
            $avatar_name = $request->newAvatar->getClientOriginalName();
            Storage::disk('image')->putFileAs('/', $request->newAvatar, $avatar_name);
            $auth_user->avatar = $avatar_name;
        }

        $auth_user->save();
        return redirect()->route('account.settings', ['auth_user' => $auth_user])->with('success', 'Настройки изменены');
    }
}
