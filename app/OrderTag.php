<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderTag extends Model
{

    protected $fillable = [
        'order_id',
        'teg_id',
    ];
}
