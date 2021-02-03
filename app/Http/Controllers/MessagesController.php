<?php

namespace App\Http\Controllers;

use App\Mail\NotificationMail;
use App\Message;
use App\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Support\Facades\Mail;

class MessagesController extends Controller
{
    public function addMessage(Request $request)
    {
        $user_data = Auth::user();
        $new_massage = new Message();
        $new_massage->order_id = $request->order_id; // id заявки (проекту)
        $new_massage->message = $request->message;
        $new_massage->from = $user_data->id;      /* від кого user_id  */
        $new_massage->to = $request->user_id;    /* до кого user_id  */
        $new_massage->rect = 0;             /* 0-нове 1-прочитано */
        $new_massage->from_name = '';       /* від кого імя */
        $new_massage->to_name = $user_data->login;/* імя кому повідомлення */
        $new_massage->save();

        $new_notif = new Notifications();
        $new_notif->order_id = $request->order_id;
        $new_notif->user_id = $request->user_id;
        $new_notif->text_notif = "Поступил новый ответ по заявке от $user_data->login";
        $new_notif->type = 'new_request_comment';
        $new_notif->color = 'info';
        $new_notif->status = '1';
        $new_notif->save();

        $notification = "Поступил новый ответ по заявке от $user_data->login";
        $link = $new_notif;
        Mail::to(Auth::user()->email)->send(new NotificationMail($notification, $link));
    }

    public function getMessage(Request $request)
    {
        $get_message_by_req_id = Message::leftJoin('users', 'messages.from', '=', 'users.id')
            ->select('messages.*', 'users.avatar')
            ->where('order_id', $request->order_id)->where('to', $request->to)->get();
        return response()->json([
            'messages' => $get_message_by_req_id
        ]);
    }

    public function updateRect(Request $request)
    {
        $arr_id = $request->id_array;
        if(empty($arr_id)) return;
        foreach ($arr_id as $id) {
            $message = Message::where('id', $id)->first();
            $message->rect = 1;
            $message->save();
        }

    }
}
