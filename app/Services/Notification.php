<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;

class Notification
{
    public function allNotification()
    {
        $user_id = Auth::id();
        $notifications = \App\Notifications::where('user_id', $user_id)->where('status', 1)->orderBy('created_at', 'desc')->get()->toArray();
        return [
        	'notifications' => $notifications,
            'notifications_count' => count($notifications),
        ];
    }
}