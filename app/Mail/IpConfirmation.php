<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpParser\Node\Stmt\UseUse;
use Ramsey\Uuid\Uuid;
use App\IpConfirmation as ipc;
use Illuminate\Support\Facades\DB;

class IpConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     * @param integer $ip , unsigned integer $user_id
     * @return void
     */
    protected $token,$name,$ip,$code;
    public function __construct($token,$name,$ip,$code)
    {
        $this->token = $token;
        $this->name=$name;
        $this->ip=$ip;
        $this->code=$code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from("noreply@thebro.exchange")->subject("Confirm new IP login | ".env("APP_NAME"))->view('mail.ipConfirmation')->with(["token" => $this->token,"name"=>$this->name,"ip"=>$this->ip,'code'=>$this->code]);
    }
}
