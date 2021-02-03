<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'text_notif',
        'type',
        'color',
        'status',
    ];
}
