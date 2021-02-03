<?php

namespace App\Mail;

use App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $notification;
    protected $link;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($notification, $link)
    {
        $this->notification = $notification;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Сообщение от Twoe.io!')->view('layouts.notification_mail', ['notification' => $this->notification, 'link' => $this->link]);
    }
}
