<?php

namespace App\Jobs;

use App\SupportTicketText;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use App\SupportTicket;
use App\Mail\SupportTicketMail;
use Illuminate\Support\Facades\Mail;

class SupportTicketMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $type, $ticket_id,$subject,$admin,$last_messages,$name,$user,$email;
    public function __construct($type, $ticket_id,$admin=0)
    {
        $this->admin=$admin;
        $this->ticket_id = $ticket_id;
        $this->user = User::find(SupportTicket::find($ticket_id)->user_id);
        $this->type=$type;
        if ($this->type=='opened')
        {
            $this->subject="Support ticket opened";
        }
        else if ($this->type=='added')
        {
            $this->subject="You have added to support ticket successfully";
        }
        else if ($this->type=='closed')
        {
            $this->subject="Your ticket was closed";
        }
        else if ($this->type=='answered')
        {
            $this->subject="Your ticket was answered";
        }
        /*$m = SupportTicketText::where('ticket_id',$this->ticket_id)->orderByDesc('id')->limit(5)->get();
        $timezone = $this->user->timezone;
        for ($i=0;$i<count($m);$i++)
        {
            $m[$i]->created_at=Carbon::createFromTimeString($m[$i]->created_at)->tz($timezone)->format("Y-m-d H:i:s");
            $m[$i]->updated_at=Carbon::createFromTimeString($m[$i]->updated_at)->tz($timezone)->format("Y-m-d H:i:s");
        }
        $this->last_messages=$m;*/
        $this->subject .= " | ".env("APP_NAME");
        $this->email = $this->user->email;
        if ($this->admin===1)
        {
            $this->email=env("ADMIN_EMAIL");
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->queue(new SupportTicketMail($this->type, $this->ticket_id,$this->user->name,$this->admin,$this->subject,$this->user->anticode->code));
    }
}
