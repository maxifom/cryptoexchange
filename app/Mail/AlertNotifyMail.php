<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AlertNotifyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $text,$code;
    public function __construct($text,$code)
    {
        $this->text=$text;
        $this->code=$code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@thebro.exchange')->subject('Alert Notify:'.$this->text." | ".env("APP_NAME"))->view('mail.alertNotify')->with(['text'=>$this->text,'code'=>$this->code]);
    }
}
