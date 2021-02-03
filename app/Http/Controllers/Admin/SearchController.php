<?php

namespace App\Http\Controllers\Admin;

use App\Order;
use App\OrderRequest;
use App\Reviews;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('query');
        $orders = [];
        $reviews = [];
        $order_requests = [];
        $users = [];

        $request->validate([
            'query' => 'required'
        ]);

        if ($query) {
            $orders = Order::leftJoin('users', 'orders.user_id', 'users.id')
                ->select('orders.*', 'users.login', 'users.avatar')
                ->where('title', 'like', "%$query%")
                ->orWhere('description', 'like', "%$query%")
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            $reviews = Reviews::leftJoin('users', 'reviews.from_user_id', 'users.id')
                ->select('reviews.*', 'users.avatar', 'users.first_name', 'users.last_name', 'users.login')
                ->where('text_review', 'like', "%$query%")
                ->orWhere('users.login', 'like', "%$query%")
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            $order_requests = OrderRequest::where('text_request', 'like', "%$query%")
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            $users = User::where('login', 'like', "%$query%")
                ->orWhere('first_name', 'like', "%$query%")
                ->orWhere('last_name', 'like', "%$query%")
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();
        }

        return view('admin.search.index',[
            'query' => $query,
            'orders' => $orders,
            'reviews' => $reviews,
            'order_requests' => $order_requests,
            'users' => $users
        ]);
    }
}
