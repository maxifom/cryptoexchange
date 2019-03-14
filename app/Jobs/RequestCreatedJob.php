<?php

namespace App\Jobs;

use App\Mail\RequestCreatedMail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\CoinRequest;
use Illuminate\Support\Facades\Mail;

class RequestCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries=3;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $request;
    public function __construct($request)
    {
        $this->request=$request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::find($this->request->user_id);
        $subject = "Coin request ";
        if ($this->request->status=='created')
        {
            $subject.="created";
        }
        else if ($this->request->status=='under_review')
        {
            $subject.=" under review";

        }
        else if ($this->request->status=='confirmed')
        {
            $subject.="confirmed";
        }
        if ($this->request->status=='created')
        {
            $admin = User::where('admin',1)->first();
            $code=$admin->anticode->code;
            Mail::to(env("ADMIN_EMAIL"))->queue(new RequestCreatedMail($this->request->name,$this->request->status, 'Admin',$code,$subject));
        }
        Mail::to($user->email)->queue(new RequestCreatedMail($this->request->name,$this->request->status,$user->name,$user->anticode->code,$subject));
    }
}
