<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Storage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Denpa\Bitcoin\Client as BitcoinClient;
use App\Withdrawal;
use App\Events\WithdrawalApproved;
use App\Events\WithdrawalRequested;
use App\Events\WithdrawalSent;
use Illuminate\Support\Facades\DB;

class ProcessWithdrawal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $withdrawal_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->withdrawal_id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function () {
            if (!Storage::disk('local')->exists("current_withdrawals/" . $this->withdrawal_id)) {
                Storage::disk('local')->put("current_withdrawals/" . $this->withdrawal_id, "1");
            }
            $withdrawal = Withdrawal::where('id',$this->withdrawal_id)->lockForUpdate()->first();
            if ($withdrawal->status=='requested')
            {
                Storage::disk('local')->delete("current_withdrawals/" . $this->withdrawal_id);
                return;
            }
            if ($withdrawal->status=='approved')
            {
                $value = $withdrawal->value;
                $address = $withdrawal->address;
                $coin = $withdrawal->wallet->coin;
                $bitcoind = new BitcoinClient('http://' . $coin['user'] . ':' . $coin['pass'] . '@localhost:' . $coin['port'] . '/');
                $listunspent = $bitcoind->listunspent()->get();
                $sum = 0.0;
                foreach ($listunspent as $unspent) {
                    $sum += $unspent['amount'];
                }
                $val1 = floatval(number_format($value, 8, '.', ''));
                if ($sum >= $val1) {
                    $tx = $bitcoind->sendToAddress($address, $val1)->get();
                    $withdrawal->tx = $tx;
                    $withdrawal->status = "sent";
                    $withdrawal->save();
                    event(new WithdrawalSent($withdrawal, $tx));
                } else if ($withdrawal->status = "approved") {
                    event(new WithdrawalApproved($withdrawal));
                } else if ($withdrawal->status = "requested") {
                    event(new WithdrawalRequested($withdrawal));
                }
            }
            Storage::disk('local')->delete("current_withdrawals/" . $this->withdrawal_id);
        },1);

    }

    public function failed(Exception $exception)
    {
        Storage::disk('local')->delete("current_withdrawals/" . $this->withdrawal_id);
    }
}
