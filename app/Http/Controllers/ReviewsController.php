<?php

namespace App\Http\Controllers;

use App\Mail\NotificationMail;
use App\Message;
use App\Notifications;
use App\Order;
use App\OrderRequest;
use App\Reviews;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReviewsController extends Controller
{
    public function addReviews(Request $request, $id)
    {
        $request->validate([
            'text_review' => 'required'
        ]);

        $reviews = new Reviews();
        $reviews->user_id = $id;
        $reviews->from_user_id = $request->from_user_id;
        $reviews->order_id = $request->order_id;
        $reviews->status = $request->status;
        $reviews->text_review = $request->text_review;
        $reviews->save();

        $order = Order::where('id', $request->order_id)->first();

        if ($reviews->count() == 2){
            $order->status = 'done';
            $order->save();
        }

        /* створюєм нотіфікейшн */
        $new_notif = new Notifications();
        $new_notif->order_id = $request->order_id;
        $new_notif->user_id = $request->from_user_id;
        if ($request->status == 'positive') {
            $new_notif->text_notif = "Вам оставили положительний отзыв по проекту - " . $order->title;
            $new_notif->type = 'new_good_review';
            $new_notif->color = 'success';
        }
        if($request->status == 'neutral') {
            $new_notif->text_notif = "Вам оставили нейтральный отзыв по проекту - " . $order->title;
            $new_notif->type = 'new_good_review';
            $new_notif->color = 'success';
        }
        elseif ($request->status == 'negative') {
            $new_notif->text_notif = "Вам оставили негативный отзыв по проекту - " . $order->title;
            $new_notif->type = 'new_bad_review';
            $new_notif->color = 'danger';
        }
        $new_notif->status = 1;
        $new_notif->save();

        $user = User::where('id', $request->from_user_id)->select('email')->first();
        $notification = $new_notif->text_notif;
        $link = $new_notif;
        Mail::to($user->email)->send(new NotificationMail($notification, $link));

        return redirect()->route('order.success', ['order_id' => $request->order_id]);
    }

}
