<?php

namespace App\Mail;

use App\SupportTicket;
use App\SupportTicketText;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class SupportTicketMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $type, $ticket_id,$subject,$admin,$name,$code;
    public function __construct($type,$ticket_id,$name,$admin,$subject,$code)
    {
        $this->admin=$admin;
        $this->name=$name;
        $this->ticket_id = $ticket_id;
        $this->type=$type;
        $this->subject = $subject;
        $this->code=$code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->from(env("MAIL_FROM_ADDRESS"))->view('mail.supportTicket')->with([
            'admin'=>$this->admin,
            'type'=>$this->type,
            'ticket_id'=>$this->ticket_id,
            'name'=>$this->name,
            'code'=>$this->code
        ]);
    }
}
