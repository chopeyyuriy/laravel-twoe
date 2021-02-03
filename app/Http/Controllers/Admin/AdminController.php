<?php

namespace App\Http\Controllers\Admin;

use App\Order;
use App\Reviews;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $statistic = DB::select('Select (Select count(*) From users) as user_count, (SELECT COUNT(*) FROM orders WHERE status = ?) AS order_count_active, (SELECT COUNT(*) FROM orders WHERE status = ?) AS order_count_done, (SELECT COUNT(*) FROM order_requests) AS order_requests', array('active', 'done'));

        $all_orders = Order::leftJoin('users', 'orders.user_id', 'users.id')
            ->select('orders.*', 'users.login', 'users.avatar', 'users.ip')
            ->orderBy('orders.created_at', 'desc')
            ->get()
            ->take(5);

        $all_reviews = Reviews::leftJoin('users', 'reviews.from_user_id', 'users.id')
            ->select('reviews.*', 'users.avatar', 'users.first_name', 'users.last_name', 'users.login')
            ->orderBy('reviews.created_at', 'desc')
            ->get()
            ->take(5);

        return view('admin.dashboard.dashboard', ['all_orders' => $all_orders, 'all_reviews' => $all_reviews, 'statistic' => $statistic]);
    }

    public function show($id)
    {
        $order = Order::leftJoin('users', 'orders.user_id', 'users.id')
            ->select('orders.*', 'users.login', 'users.avatar', 'users.ip')
            ->where('orders.id', $id)
            ->orderBy('orders.created_at', 'desc')
            ->first();

        return response()->json([
            'order' => $order
        ]);
    }


}
