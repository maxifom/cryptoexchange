<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\EmailConfirmation;
use Illuminate\Support\Facades\DB;
use App\Mail\EmailConfirmationMail;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Mail;

class EmailConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $user, $token, $resend;

    public function __construct($user, $resend = false)
    {
        $this->user = $user;
        $this->token = str_replace("-", "", Uuid::uuid4());
        $this->resend = $resend;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->user->confirmed === 0) {
            $r = DB::transaction(function () {
                $e = EmailConfirmation::firstOrCreate(["user_id" => $this->user->id], ["token" => $this->token]);
                Storage::append('1.txt', $e->id . ($e->wasRecentlyCreated ? 1 : 0));
                if ($e->wasRecentlyCreated === true) {
                    return 1;
                }
                $this->token = $e->token;
                return 0;
            }, 1);
            if ($r === 1 || $this->resend) {
                Mail::to($this->user->email)->queue(new EmailConfirmationMail($this->token, $this->user->name, $this->user->anticode->code));
            }

        }

    }

}
