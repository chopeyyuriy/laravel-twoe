<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/account_requests';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function authenticated(Request $request, $user)
    {
        $user->ip = $request->ip();
        $user->update();
        if ($user->status !== User::STATUS_ACTIVE) {
            $this->guard()->logout();
            return back()->with('error', 'You need to confirm your account. Please check your email.');
        }
        if ($user->is_admin  && $request->post('admin_panel')) {
            return redirect()->intended('/admin/index');
        }
        return redirect()->intended($this->redirectPath());
    }


}
