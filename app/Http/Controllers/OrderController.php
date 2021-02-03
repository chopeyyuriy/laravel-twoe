<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequestsValid;
use App\Http\Requests\OrdersValid;
use App\Mail\NotificationMail;
use App\Message;
use App\Notifications;
use App\Order;
use App\OrderRequest;
use App\Reviews;
use App\Tag;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        if (Auth::guest()) {
            return redirect()->route('index')->with('success', 'Для подачи заявки нужно авторизоваться в системе !');
        } else {
            $tags = Tag::where('popular', 1)->get();
            return view('request_new', compact('tags'));
        }
    }

    public function save(OrdersValid $request)
    {
        $new_order = new Order();
        $new_order->saveAndUpdateOrder($new_order, $request);
        return redirect()->route('account.requests');
    }

    public function edit($id)
    {
        $order = Order::findOrFail($id);
        if ($order->user_id == Auth::id()) {
            $all_tags = Tag::where('popular', 1)->get();
            return view('request_updates', ['order' => $order, 'all_tags' => $all_tags]);
        } else {
            return redirect()->route('index')->with('success', 'Ошибка! Вы пытаетесь изменить чужую заявку !');
        }
    }

    public function update(Request $request, $id)
    {
        $update_order = Order::findOrFail($id);
        $update_order->saveAndUpdateOrder($update_order, $request);
        return redirect()->route('account.requests');
    }

    public function show(Request $request, $id)
    {
        $login_user_id = Auth::id();
        $order_data = Order::findOrFail($id);
        if ($order_data->executor_id) {
            return redirect()->route('order.success', $order_data->id);
        } else {
            $user_data = User::where('id', $order_data->user_id)->first();
            $notif_id = $request->read;

            $allOrderRequest = $order_data->getOrderRequest($id, $login_user_id, $notif_id);

            $get_messages_by_id = Message::leftJoin('users', 'users.id', '=', 'messages.from')
                ->select('messages.*', 'users.login', 'users.avatar', 'users.review_positive', 'users.review_neutral', 'users.review_negative')
                ->where('order_id', $id)->get();

            return view('request', [
                'order_id' => $id,
                'order_data' => $order_data,
                'user_data' => $user_data,
                'login_user_id' => $login_user_id,
                'get_order_request' => $allOrderRequest['get_order_request'],
                'login_user_request' => $allOrderRequest['login_user_request'],
                'rest_login_request' => $allOrderRequest['rest_login_request'],
                'user_message_id' => $allOrderRequest['user_message_id'],
                'get_messages_by_id' => $get_messages_by_id,
            ]);
        }

    }

    public function saveOrderRequests(OrderRequestsValid $request, $id)
    {
        $login_user_id = Auth::id();
        $login_user_login = User::where('id', $login_user_id)->select('login')->first();
        $order_data = Order::where('id', $id)->first();
        $user_data = User::where('id', $order_data->user_id)->first();
        $notif_id = $request->read;

        $allOrderRequest = $order_data->getOrderRequest($id, $login_user_id, $notif_id);

        if ($request->method() == 'POST') {
            $save_order_request = new OrderRequest();
            $save_order_request->order_id = $id;
            $save_order_request->user_id = $login_user_id;
            $save_order_request->text_request = $request->text_request;
            $save_order_request->status = 'active';
            $save_order_request->save();

            if ($save_order_request) {
                $new_notif = new Notifications();
                $new_notif->order_id = $id;
                $new_notif->user_id = $order_data->user_id;
                $new_notif->text_notif = "Поступило новое предложение на заявку $order_data->title от пользователя $login_user_login->login";
                $new_notif->type = 'new_request';
                $new_notif->color = 'info';
                $new_notif->status = 1;
                $new_notif->save();

                $notification = "Поступило новое предложение на заявку $order_data->title от пользователя $login_user_login->login";
                $link = $new_notif;
                Mail::to($user_data->email)->send(new NotificationMail($notification, $link));
            }

            return redirect('/order/' . $save_order_request->order_id);
        }

        return view('request', [
            'order_id' => $id,
            'order_data' => $order_data,
            'user_data' => $user_data,
            'login_user_id' => $login_user_id,
            'get_order_request' => $allOrderRequest['get_order_request'],
            'login_user_request' => $allOrderRequest['login_user_request'],
            'rest_login_request' => $allOrderRequest['rest_login_request']
        ]);
    }


    public function editOrderRequests(Request $request)
    {
        $id = $request->id;
        $user_id = Auth::id();
        $etit_sequest = OrderRequest::where(['user_id' => $user_id, 'id' => $request->id])->first();
        if ($user_id == $etit_sequest->user_id) {
            $etit_sequest->text_request = $request->text_request;
            $status = $etit_sequest->save();
        }
        return redirect()->route('order.show', ['id' => $etit_sequest->order_id]);

    }

    public function deleteOrderRequests(Request $request)
    {
        $id = $request->id;
        $delete = OrderRequest::where('id', $id)->delete();
        if ($delete) {
            ///Message::where('id', $id)->delete();
        }
        return response()->json([
            'status' => $delete ? 'ok' : 'fail'
        ]);
    }

    public function repostOrder($id)
    {
        $get_order_by_id = Order::where('id', $id)->first();
        $get_order_by_id->status = 'active';
        $get_order_by_id->created_at = date('Y-m-d H:i:s');
        $get_order_by_id->save();

        return redirect('/order/' . $id);
    }

    public function closeOrder($id)
    {
        $close_order = Order::where('id', $id)->first();
        $close_order->status = 'closed';
        $close_order->save();

        return redirect('/order/' . $id);
    }

    public function acceptUserRequests(Request $request, $user_id)
    {
        if ($request->checked == 'on') {
            $order_id = $request->order_id;
            $get_order = Order::where('id', $order_id)->first();
            if ($get_order->executor_id == "") {
                $get_order->executor_id = $user_id;
                $get_order->status = 'in_progress';
                $get_order->save();

                $new_notif = new Notifications();
                $new_notif->order_id = $order_id;
                $new_notif->user_id = $user_id;
                $new_notif->text_notif = "Вас выбрали исполнителем проекта " . $get_order->title;
                $new_notif->type = 'confirm_request';
                $new_notif->status = 1;
                $new_notif->color = 'warning';
                $new_notif->save();

                $notification = "Вас выбрали исполнителем проекта " . $get_order->title;
                $link = $new_notif;
                Mail::to(Auth::user()->email)->send(new NotificationMail($notification, $link));

                return redirect()->route('order.success', $request->order_id);
            } else {
                return redirect()->route('order.show', $order_id)->with('fail', 'К заявке уже назначен исполнитель !');
            }
        }
    }

    public function successOrder($order_id)
    {
        $order = Order::where('orders.id', $order_id)->first();
        $user_data = User::where('id', $order->user_id)->first();

        $executor = User::where('id', $order->executor_id)->first();
        $executor_request = OrderRequest::where('order_id', $order_id)->where('user_id', $order->executor_id)->first();

        $reviews_order = Reviews::where('order_id', $order_id)->first();

        if (isset(Auth::user()->id)) {
            if (Auth::user()->id == $order->executor_id){
                $login_user = $order->executor_id;
                $reviews_login = Reviews::where('user_id', $login_user)->first();
            } elseif(Auth::user()->id == $order->user_id) {
                $login_user = $user_data->id;
                $reviews_login = Reviews::where('user_id', $login_user)->first();
            } else {
                $login_user = Auth::user()->id;
                $reviews_login = Reviews::get();
                if ($reviews_login->count() == 0){
                    $reviews_login = null;
                }
            }
        } else {
            $login_user = null;
            $reviews_login = Reviews::get();
            if ($reviews_login->count() == 0){
                $reviews_login = null;
            }
        }

        $reviews = Reviews::Join('users', 'reviews.user_id', 'users.id')
            ->select('reviews.*', 'users.avatar', 'users.login', 'users.created_at', 'users.review_positive', 'users.review_neutral', 'users.review_negative')
            ->get();

        $executor_messages = Message::Join('users', 'messages.from', 'users.id')
            ->select('messages.*', 'users.avatar', 'users.login', 'users.created_at')
            ->where('order_id', $order_id)
            ->where('to', $order->executor_id)
            ->get();

        return view('request-customer-success',
            ['order' => $order,
                'user_data' => $user_data,
                'executor' => $executor,
                'executor_request' => $executor_request,
                'executor_messages' => $executor_messages,
                'reviews' => $reviews,
                'reviews_login' => $reviews_login,
                'login_user' => $login_user,
                'reviews_order' => $reviews_order
            ]);
    }

}
