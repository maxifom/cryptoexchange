<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WithdrawalConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $token,$coin,$address,$amount,$name,$code;
    public function __construct($token,$coin,$address,$amount,$name,$code)
    {
        $this->token = $token;
        $this->coin = $coin;
        $this->address = $address;
        $this->amount = $amount;
        $this->name = $name;
        $this->code=$code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@thebro.exchange')->subject('Confirm your withdrawal | '.env("APP_NAME"))->view('mail.withdrawalConfirmation')->with(['token'=>$this->token,'name'=>$this->name,'coin'=>$this->coin,'amount'=>$this->amount,'address'=>$this->address,'code'=>$this->code]);
    }
}
