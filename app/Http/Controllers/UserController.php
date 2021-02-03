<?php

namespace App\Http\Controllers;

use App\Order;
use App\Reviews;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function index($id)
    {
        $dogs = '@';
        $user_data = User::findOrFail($id);
        $count_orders = Order::where('user_id', $user_data->id)->where('active', 1)->count();

        $reviews = Reviews::leftJoin('users', 'reviews.from_user_id', 'users.id')
            ->leftJoin('orders', 'reviews.order_id', 'orders.id')
            ->select('reviews.*', 'users.avatar', 'users.login', 'orders.title')
            ->get();

        $positive = Reviews::where('status', 'positive')->count();
        $neutral = Reviews::where('status', 'neutral')->count();
        $negative = Reviews::where('status', 'negative')->count();

        return view('reviews_autorised', [
            'user_data' => $user_data,
            'dogs' => $dogs,
            'count_orders' => $count_orders,
            'reviews' => $reviews,
            'positive' => $positive,
            'neutral' => $neutral,
            'negative' => $negative]);
    }

}
