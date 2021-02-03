<?php

namespace App\Http\Controllers;

use App\Notifications;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function closeNotifications(Request $request)
    {
        $user_id = Auth::id();
        $getNotification = Notifications::where('id', $request->id)->first();
        if ($user_id == $getNotification->user_id) {
            $getNotification->status = 0;
            $getNotification->save();
        }
        $coutAllNotification = Notifications::where('user_id', $user_id)->where('status', 1)->count();

        return response()->json([
            'count' => $coutAllNotification
        ]);

    }

    public function getAllNotifications(Request $request)
    {
        $user_id = Auth::id();
        $allNotifications = Notifications::leftJoin('orders', 'orders.id', '=', 'notifications.order_id')
            ->leftJoin('users', 'users.id', '=', 'notifications.user_id')
            ->select('notifications.*', 'orders.title', 'users.login')
            ->where('notifications.user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('account_alerts', ['allNotifications' => $allNotifications]);
    }

    public function loadMoreNotification(Request $request)
    {
        $user_id = Auth::id();
        $n_id = $request->n_id;
        $notifications = Notifications::leftJoin('users', 'users.id', '=', 'notifications.user_id')
            ->select('notifications.*', 'users.login')
            ->where('notifications.user_id', $user_id)
            ->where('notifications.id', '<', $n_id)
            ->orderBy('created_at', 'desc')
            ->paginate(1);

        return response()->json(['notifications' => $notifications]);
    }


    public function deleteNotifications(Request $request)
    {
        Notifications::where('id', $request->id)->delete();
    }
}
