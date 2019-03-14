<?php

namespace App\Console\Commands;

use App\Mail\AlertNotifyMail;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use TrayLabs\InfluxDB\Facades\InfluxDB;
use InfluxDB\Point;
use InfluxDB\Database;

class DaemonAlert extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public $text;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alertNotify {coin} {text}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Alert notify for coin daemon';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $coin = $this->argument('coin');
        $text = $this->argument('text');
        $text .= " For coin: " . $coin;
        list($usec, $sec) = explode(' ', microtime());
        $timestamp = sprintf('%d%06d', $sec, $usec * 1000000);
        try {
            $point = [new Point
            (
                'live_update',
                null,
                ['type' => 'alert'],
                ['text' => $text],
                $timestamp
            )];
            InfluxDB::writePoints($point, Database::PRECISION_MICROSECONDS);
            $admin = User::where('admin',1)->first();
            $code=$admin->anticode->code;
            Mail::to(env("ADMIN_EMAIL"))->queue(new AlertNotifyMail($text,$code));
        } catch (\Exception $e) {
            report($e);
        }
    }
}
