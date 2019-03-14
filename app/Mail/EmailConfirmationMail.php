<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $token,$name,$code;
    public function __construct($token,$name,$code)
    {
        $this->name=$name;
        $this->token=$token;
        $this->code=$code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@thebro.exchange')->subject('Confirm Email | '.env("APP_NAME"))->view('mail.mailConfirmation')->with(['token'=>$this->token,'name'=>$this->name,'code'=>$this->code]);
    }
}
