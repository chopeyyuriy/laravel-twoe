<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    protected $fillable = [
        'user_id',
        'from_user_id',
        'order_id',
        'text_review',
        'status',
        'rating'
    ];

    public function getAvatar()
    {
        return '/app/img/user/'.$this->avatar;
    }

    public function userFromTo($id){
        $user = User::where('id', $id)->select('login')->first();
        return $user->login;
    }
}
