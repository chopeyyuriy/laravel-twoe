<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\VerifyAccountValid;
use App\Mail\Auth\VerifyMail;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        return Validator::make($data, [
            'login' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'login' => $data['login'],
            'email' => $data['email'],
            'avatar' => User::randomAvatar(),
            'password' => Hash::make($data['password']),
            'status' => User::STATUS_INACTIVE,
            'token' => Str::random(),
        ]);
        Mail::to($user->email)->send(new VerifyMail($user));

        return $user;
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        event(new Registered($user = $this->create($request->all())));

        return redirect()->route('index')->with('success', 'Благодарим за регистрацию. Письмо с дальнейшими инструкциями отправлено на Вашу почту.');}

    public function verify(Request $request, $token)
    {

        if (!$user = User::where('token', $token)->first()) {
            return redirect()->route('index')
                ->with('error', 'Sorry your link cannot be identified.');
        }

        if ($request->method() == 'POST') {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
            ], [
                'first_name.required' => 'введите Имя!',
                'last_name.required' => 'введите Фамилию!',
            ]);

            if (!$validator->fails()) {
                $user->token = null;
                $user->status = User::STATUS_ACTIVE;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->ip = $request->ip();
                if ($user->save()) {
                    Auth::login($user);
                    return redirect($this->redirectTo);
                } else {
                    return redirect("verify/$token");
                }
            } else {
                return redirect("verify/$token")
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        return view('auth/verify_account', ['token' => $token]);

    }
}
