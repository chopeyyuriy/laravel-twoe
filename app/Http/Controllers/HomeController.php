<?php

namespace App\Http\Controllers;

use App\OrderRequest;
use App\Tag;
use App\Order;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index(Request $request)
    {
        $url = $request->url() . "/";       // строка URL для кнопок фільтра
        $url_tag = $request->tag;            // масив з тегами в URL
        $i = 0;
        if ($url_tag) {
            $url_tag = array_unique($url_tag);
            foreach ($url_tag as $key) {
                if ($i == 0) {
                    $url .= "?tag[]=" . $key;
                } else {
                    $url .= "&tag[]=" . $key;
                }
                $i++;
            }

            $orders = Order::with(['tags'])
                ->Join('users', 'orders.user_id', '=', 'users.id')
                ->join('order_tags', 'orders.id', '=', 'order_tags.order_id')
                ->join('tags', 'order_tags.tag_id', '=', 'tags.id')
                ->select('orders.*', 'users.last_name', 'users.first_name', 'users.avatar')
                ->whereIn('tags.id', $url_tag)
                ->where('orders.active', 1)
                ->orderBy('orders.id', 'desc')
                ->groupBy('orders.id')
//                ->take(15)
                ->get();
        } else {
            $orders = Order::with(['tags'])
                ->Join('users', 'orders.user_id', '=', 'users.id')
                ->select('orders.*', 'users.last_name', 'users.first_name', 'users.avatar', 'users.active')
                ->orderBy('orders.id', 'desc')
                ->groupBy('orders.id')
                ->where('orders.status', 'active')
                ->paginate(5);

        }

        $tags = DB::table('tags', 1)->select('id', 'name')->get();

        return view('index', ['orders' => $orders,
            'tags' => $tags,
            'url_tag' => $url_tag,
            'url' => $url,
        ]);
    }

    public function loadMoreOrder(Request $request)
    {
        $o_id = $request->o_id;

        $orders = Order::with(['tags'])
            ->Join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.last_name', 'users.first_name', 'users.avatar', 'users.active')
            ->where('orders.id', '<', $o_id)
            ->orderBy('orders.id', 'desc')
            ->groupBy('orders.id')
            ->where('orders.status', 'active')
            ->paginate(5);

        return view('_orders', ['orders' => $orders]);

    }
}
