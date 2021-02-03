<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login',
        'first_name',
        'last_name',
        'email',
        'password',
        'avatar',
        'status',
        'token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->is_admin; // поле is_admin в таблице users
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
//        file_put_contents(public_path('reste_password.txt'), $token . "\n", FILE_APPEND);

    }

    public function getAvatar()
    {
        return '/app/img/user/'.$this->avatar;
    }

    public function doneOrders()
    {
        $user_id = Auth::id();
        $done_order = Order::where('user_id', $user_id)->where('status', 'done')->count();
        return $done_order;
    }

    public static function randomAvatar()
    {
        $img = sprintf("%'.02d", rand(1, 10));
        return $img .'.jpg';
    }
}
