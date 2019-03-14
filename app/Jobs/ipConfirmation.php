<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\IpConfirmation as ipc;
use App\Mail\IpConfirmation as ipMail;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;
use App\User;
use Illuminate\Support\Facades\DB;
class ipConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries=3;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $ip, $user_id;
    public $token,$name;
    protected $email;
    public $code;
    public function __construct($ip, $user_id)
    {
        $this->ip = $ip;
        $this->user_id = $user_id;
        $user = User::find($user_id);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->code=$user->anticode->code;
        $this->token = str_replace("-","",Uuid::uuid4());
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function () {
            $ipc = ipc::firstOrCreate(['user_id' => $this->user_id, 'ip' => ip2long($this->ip)], ['token' => $this->token]);
            if ($ipc->wasRecentlyCreated)
            {
                Mail::to($this->email)->queue(new ipMail($this->token,$this->name,$this->ip,$this->code));
            }
        },1);
    }
}
