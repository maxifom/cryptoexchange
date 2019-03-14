<?php

namespace App\Console\Commands;

use App\Coin;
use App\CoinFunding;
use App\CoinFundingDeposit;
use App\CoinFundingDeposits;
use App\Events\TxReceived;
use App\Wallet;
use Denpa\Bitcoin\Exceptions\BitcoindException;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Denpa\Bitcoin\Client;
use App\Deposit;
use Illuminate\Support\Facades\DB;

class newTx extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newTx {coin} {tx}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'New tx to wallet';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {

            $tx_received_time = date('Y-m-d H:i:s', date(time()));
            $outputs = new Collection;
            //$deposited = new Collection;
            // $wallets_addresses = new Collection;
            $coin_name = $this->argument("coin");
            $tx_hash = $this->argument("tx");
            $coin = Coin::where('name', $coin_name)->first();
            //$wallets = Wallet::where('coin_id', $coin->id)->get();
            $bitcoind = new Client('http://' . $coin->user . ':' . $coin->pass . '@localhost:' . $coin->port . '/');
            $tx = $bitcoind->getRawTransaction($tx_hash, 1)->get();
            /*$rpcClient = new Client('http://localhost:' . $coin['port']);
            $rpcClient->getHttpClient()
                ->withUsername($coin['user'])
                ->withPassword($coin['pass']);
            $tx = $rpcClient->execute('getrawtransaction', [$tx_hash, 1]);*/
            //$tx_time = $tx['time'];
            $vout = $tx['vout'];
            for ($i=0;$i<count($vout);$i++)
            {
                if (isset($vout[$i]["scriptPubKey"]) && $vout[$i]["scriptPubKey"]["addresses"][0] != null) {
                    $confirmations = 0;
                    if (isset($vout[$i]['confirmations'])) {
                        $confirmations = $vout[$i]['confirmations'];
                    }
                    if (isset($vout[$i]['n']))
                    {
                        $n=$vout[$i]['n'];
                    }
                    else
                    {
                        $n=$i;
                    }
                    $outputs->push(["address" => $vout[$i]["scriptPubKey"]["addresses"][0], "value" => $vout[$i]['value'], "confirmations" => $confirmations,'n'=>$n]);
                }

            };
            foreach ($outputs as $_output) {
                DB::transaction(function () use ($_output, $coin, $tx_hash, $tx_received_time) {
                    $wallet = Wallet::where('address', $_output['address'])->where('coin_id', $coin->id)->lockForUpdate()->first();
                    $coin_funding = CoinFunding::where('address',$_output['address'])->where('coin_id',$coin->id)->lockForUpdate()->first();
                    if ($wallet == null && $coin_funding==null) {
                        return;
                    }
                    else if ($wallet!=null)
                    {
                        $d = Deposit::firstOrCreate([
                            "tx" => $tx_hash,
                            "value" => $_output['value'],
                            "wallet_id" => $wallet->id,
                            "n"=>$_output['n']],
                            ["tx_time" => $tx_received_time, "confirmations" => $_output['confirmations']]);
                        if ($d->wasRecentlyCreated == true) {
                            event(new TxReceived($d));
                        }
                    }
                    else if ($coin_funding!=null&&$coin_funding->funded===0)
                    {
                        $fd = CoinFundingDeposit::firstOrCreate([
                            "tx" => $tx_hash,
                            "value" => $_output['value'],
                            "funding_id"=>$coin_funding->id
                        ],
                            ["confirmations" => $_output['confirmations']]);
                    }
                },1);

            }
            /*foreach ($wallets as $wallet) {
                if ($wallet['address'] != null) {
                    $wallets_addresses->push($wallet['address']);
                }

            }
            foreach ($outputs as $output) {
                if ($wallets_addresses->contains($output['address'])) {
                    $output['wallet_id'] = Wallet::where("address", $output['address'])->where("coin_id", $coin->id)->select('id')->first()->id;
                    $deposited->push($output);
                }
            }
            $d = $deposited->groupBy("address");
            $final_values = new Collection;
            foreach ($d as $d1) {
                $sum = $d1->sum("value");
                $wallet_id = $d1[0]['wallet_id'];
                $address = $d1[0]['address'];
                $final_values->push(["value" => $sum, "wallet_id" => $wallet_id, "address" => $address, "confirmations" => $confirmations]);
            };
            foreach ($final_values as $deposit) {
                DB::transaction(function () use ($tx_hash,$deposit,$tx_received_time){
                    $d = Deposit::firstOrCreate(["tx" => $tx_hash, "value" => $deposit['value'], "wallet_id" => $deposit['wallet_id']], ["tx_time" => $tx_received_time, "confirmations" => $deposit['confirmations']]);
                    if ($d->wasRecentlyCreated == true) {
                        event(new TxReceived($d));
                    }
                });
            }
    */
        }
        catch (BitcoindException $e)
        {
            report ($e);
        }
    }
}
