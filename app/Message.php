<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'id',
        'from',
        'to',
        'message',
        'send',
        'rect',
        'from_name',
        'to_name',
    ];

    public function getAvatar()
    {
        return '/app/img/user/' . $this->avatar;
    }
}
