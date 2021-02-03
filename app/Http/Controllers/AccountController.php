<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderRequest;
use App\OrderTag;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'myOffers']]);
    }

    public function index()
    {
        $user_id = Auth::id();
        $orders = Order::with(['tags'])->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.last_name', 'users.first_name', 'users.avatar')
            ->where('orders.user_id', $user_id)
            ->orderBy('orders.id', 'desc')
            ->take(15)
            ->get();


        $news = [];
        $actives = [];
        $dones = [];
        foreach ($orders as $order) {
            if ($order->status == 'new') {
                $news[] = $order;
            } elseif ($order->status == 'active') {
                $actives[] = $order;
            } elseif ($order->status == 'done') {
                $dones[] = $order;
            }
        }

        return view('account_requests', ['news' => $news, 'actives' => $actives, 'dones' => $dones]);
    }


    public function myOffers()
    {
        $user_id = Auth::id();
        $orders_active = Order::with(['tags'])->leftJoin('order_requests', 'orders.id', '=', 'order_requests.order_id')
            ->leftJoin('users', 'orders.user_id', 'users.id')
            ->select('orders.*', 'users.avatar', 'users.first_name', 'users.login')
            ->where('order_requests.user_id', $user_id)
//            ->where('orders.status', 'active')
            ->orderBy('order_requests.created_at', 'desc')
            ->get();

        $orders_done = Order::with(['tags'])->leftJoin('order_requests', 'orders.id', '=', 'order_requests.order_id')
            ->leftJoin('users', 'orders.user_id', 'users.id')
            ->select('orders.*', 'users.avatar', 'users.first_name', 'users.login')
            ->where('order_requests.user_id', $user_id)
            ->where('orders.status', 'done')
            ->orderBy('order_requests.created_at', 'desc')
            ->get();

        return view('account_offers', ['orders_active' => $orders_active, 'orders_done' => $orders_done]);
    }


}
