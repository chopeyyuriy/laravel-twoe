<?php

namespace App\Console\Commands;

use App\Mail\NotificationMail;
use App\Notifications;
use App\Order;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class Sync extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Edit orders status time';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // перевірка актуальності заявок по даті створення
        // ставимо статус order_deadline_publication для заявок старших за 7 днів

        $orders = Order::whereRaw('created_at < DATE_SUB(current_date, interval 7 day )')->get();

        foreach ($orders as $order) {
            $order->status = 'closed';

            if ($order->save()) {
                $save_notification = new Notifications();
                $save_notification->order_id = $order->id;
                $save_notification->user_id = $order->user_id;
                $save_notification->text_notif = "У вашей заявки $order->title истек срок публикации!";
                $save_notification->type = 'order_deadline_publication';
                $save_notification->color = 'danger';
                $save_notification->status = 1;
                $save_notification->save();

                $user = User::where('id', $order->user_id)->select('email')->first();
                $notification = "У вашей заявки $order->title истек срок публикации!";
                $link = $save_notification;
                Mail::to($user->email)->send(new NotificationMail($notification, $link));
            }
        }


        return 0;
    }

}
