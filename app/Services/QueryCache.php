<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class QueryCache
{
    public function queryPutCache($val)
    {
        $time = Carbon::now()->addSeconds(5);

        if ($val == 'user_count') {
            if (Cache::has('user_count')) {
                $count = Cache::get('user_count');
            } else {
                $count = DB::table('users')->count();
                Cache::put('user_count', $count, $time);
            }
        }

        if ($val == 'order_count_active'){
            if (Cache::has('order_count_active')) {
                $count = Cache::get('order_count_active');
            } else {
                $count = DB::table('orders')->where('status', 'active')->count();
                Cache::put('order_count_active', $count, $time);
            }
        }

        if ($val == 'order_count_done'){
            if (Cache::has('order_count_done')) {
                $count = Cache::get('order_count_done');
            } else {
                $count = DB::table('orders', 'done')->where('status', 'done')->count();
                Cache::put('order_count_done', $count, $time);
            }
        }

        if ($val == 'order_requests'){
            if (Cache::has('order_requests')) {
                $count = Cache::get('order_requests');
            } else {
                $count = DB::table('order_requests')->count();
                Cache::put('order_requests', $count, $time);
            }
        }

        return $count;
    }
}