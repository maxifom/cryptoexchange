<?php

namespace App\Jobs;
use App\Coin;
use App\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\WithdrawalConfirmationMail;
use App\WithdrawalConfirmation;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Wallet;
use Illuminate\Support\Facades\Mail;
class WithdrawalConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $token,$withdrawal_id,$name,$coin,$address,$amount,$code;
    public function __construct($withdrawal_id,$name)
    {
        $this->withdrawal_id=$withdrawal_id;
        $withdrawal = Withdrawal::find($this->withdrawal_id);
        $this->name = $name;
        $wallet = Wallet::find($withdrawal->wallet_id);
        $user = User::find($wallet->user_id);
        $this->code=$user->anticode->code;
        $coin = Coin::find($wallet->coin_id);
        $this->coin= $coin->name;
        $this->amount = $withdrawal->value;
        $this->address = $withdrawal->address;
        $this->token = str_replace("-","",Uuid::uuid4());
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $r=DB::transaction(function(){
            WithdrawalConfirmation::create(["withdrawal_id"=>$this->withdrawal_id,"token"=>$this->token]);
            return 1;
        },1);
        if ($r==1)
        {
            $user = User::find(Wallet::find(Withdrawal::find($this->withdrawal_id)->wallet_id)->user_id);
            Mail::to($user->email)->queue(new WithdrawalConfirmationMail($this->token,$this->coin,$this->address,$this->amount,$this->name,$this->code));
        }
    }
}
