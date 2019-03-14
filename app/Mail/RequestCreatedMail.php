<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $name,$type,$user_name,$code,$subject;
    public function __construct($name,$type,$user_name,$code,$subject)
    {
        $this->name=$name;
        $this->type=$type;
        $this->user_name=$user_name;
        $this->code=$code;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@thebro.exchange')->subject($this->subject.' | '.env("APP_NAME"))->view('mail.requestCreated')->with(['name'=>$this->name,'type'=>$this->type,'user_name'=>$this->user_name,'code'=>$this->code]);
    }
}
