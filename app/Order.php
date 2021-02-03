<?php

namespace App;

use App\Mail\NotificationMail;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class Order extends Model
{

    public function tags()
    {
        return $this->belongsToMany('App\Tag', 'order_tags', 'order_id', 'tag_id');
    }

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'active',
        'status'
    ];

    public function getAvatar()
    {
        return '/app/img/user/' . $this->avatar;
    }

    public function doneOrders()
    {
        $user_id = Auth::id();
        $done_order = Order::where('user_id', $user_id)->where('status', 'done')->count();
        return $done_order;
    }

    public function saveAndUpdateOrder($order, $request)
    {
        $data_sum_order = Order::where('user_id', Auth::user()->id)
            ->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), ">=", date("Y-m-d"))
            ->count();

        if ($data_sum_order <= 15) {
            $data_last_order = Order::where('user_id', Auth::user()->id)
//                ->select('created_at')
                ->orderBy('created_at', 'desc')
                ->first();

            $date = new DateTime;
            $date->modify('-1 minutes');
            $curently_data = $date->format('Y-m-d H:i:s');


            if (isset($data_last_order->attributes["created_at"])) {
//                if ($data_last_order->attributes["created_at"] <= $curently_data) {
                $order->title = $request->title;
                $order->description = $request->description;
                $order->active = 1;
                $order->status = 'new';  // змінити на new
                $order->user_id = Auth::user()->id;
                $isNew = is_null($order->created_at);
                $order->save();
//                } else {
//                    return redirect()->route('request_new')->with('success', 'Лимит времени подачи новых заявок превышен!');
//                }
            } else {
                $order->title = $request->title;
                $order->description = $request->description;
                $order->active = 1;
                $order->status = 'new';
                $order->user_id = Auth::user()->id;
                $isNew = is_null($order->created_at);
                $order->save();
            }

        } else {
            return redirect()->route('request_new')->with('success', 'Лимит заявок на сегодня превышен!');
        }

        if (is_array($request->tags)) {
            $teg_id = [];
            foreach ($request->tags as $tag) {
                $new_teg = Tag::where('name', $tag)->first();
                if (!$new_teg) {
                    $new_teg = new Tag();
                    $new_teg->name = preg_replace('/[^\w]+/u', '', $tag);
                    $new_teg->save();
                }
                $teg_id[] = $new_teg->id;
            }
            $order->tags()->sync($teg_id);
        }
        if ($isNew) {
            $new_notif = new Notifications();
            $new_notif->order_id = $order->id;
            $new_notif->user_id = Auth::user()->id;
            $new_notif->text_notif = "Ваша заявка '$request->title' создана и отправлена на модерацию";
            $new_notif->type = 'new_order';
            $new_notif->color = 'success';
            $new_notif->status = 1;
            $new_notif->save();

            $notification = "Ваша заявка '$request->title' создана и отправлена на модерацию";
            $link = $new_notif;
            Mail::to(Auth::user()->email)->send(new NotificationMail($notification, $link));
        }
    }

    public function getOrderRequest($id, $login_user_id, $notif_id)
    {
        $order_data = Order::where('id', $id)->first();
        $user_data = User::where('id', $order_data->user_id)->first();

        $get_order_request = OrderRequest::leftJoin('users', 'order_requests.user_id', 'users.id')
            ->select('order_requests.*', 'users.avatar', 'users.first_name', 'users.login', 'users.review_positive', 'users.review_neutral', 'users.review_negative')
            ->where('order_requests.order_id', $id)
            ->orderBy('order_requests.order_id', 'desc')
            ->get();

        $update_status_notif = Notifications::where('id', $notif_id)->first();
        if ($update_status_notif) {
            $update_status_notif->status = '0';
            $update_status_notif->save();
        }

        $login_user_request = [];
        $rest_login_request = [];
        foreach ($get_order_request as $order_request) {
            if ($order_request->user_id == $login_user_id) {
                $login_user_request = [$order_request];
            } else {
                $rest_login_request[] = [$order_request];
            }
        }

        $get_messages = Message::leftJoin('users', 'messages.from', 'users.id')
            ->select('messages.*', 'users.avatar', 'users.login', 'users.review_positive', 'users.review_neutral', 'users.review_negative')
            ->where('order_id', $id)->get();
        $user_message_id = [];
        foreach ($get_messages as $message) {
            $user_message_id[$message->to][] = $message;
        }
        return ['order_data' => $order_data,
            'user_data' => $user_data,
            'get_order_request' => $get_order_request,
            'login_user_request' => $login_user_request,
            'rest_login_request' => $rest_login_request,
            'user_message_id' => $user_message_id];
    }

    public function formatForBot()
    {
        /*Описаниє*/
        $desc = mb_substr(strip_tags($this->description), 0, 150);
        $desc = html_entity_decode($desc);

        /*Теги*/
        $tags = [];
        foreach ($this->tags as $tag) {
            $tags[] = '#' . $tag->name;
        };
        $tags = implode(", ", $tags);

        $link = url()->to('/');

        $html = "<a href=\"$link/order/$this->id\">$this->title</a>\n"
            . "$desc " . "<a href=\"$link/order/$this->id\">подробнее</a>\n"
            . '<b>Хештеги: </b>'
            . "$tags";


        return $html;
    }

    public function sendToTelegram()
    {
        $results = Telegram::getUpdates();

        $html = $this->formatForBot();
        foreach ($results as $row) {
            $chatId = $row['message']['chat']['id'] ?? false;

            if (!$chatId) {
                continue;
            }
            $response = Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $html,
                'parse_mode' => 'HTML',
            ]);


            $messageId = $response->getMessageId();
            if (!$messageId) {
                Log::error('telegram get telegram');
            }
        }
    }
}
