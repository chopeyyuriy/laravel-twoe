<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderRequest extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'text_request',
        'status',
    ];

    public function getAvatar()
    {
        return '/app/img/user/'.$this->avatar;
    }
}
