<?php

namespace App\Console\Commands;

use App\Coin;
use App\Mail\NotifyCollectdMail;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use JsonRPC\Client;
use TrayLabs\InfluxDB\Facades\InfluxDB;

class CollectdNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collectd:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check collectd metrics';

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
        $admin = User::where('admin',1)->first();
        $code=$admin->anticode->code;
        $influxdb = new InfluxDB();
        $influxdb = $influxdb::getClient()->selectDB('server_stats');
        $cpu_value_now = $influxdb->query("SELECT value,instance FROM cpu_value WHERE type_instance='idle' ORDER BY time DESC LIMIT 8")->getPoints();
        $sum = 0;
        for ($i = 0; $i < count($cpu_value_now); $i++) {
            $sum += 100 - $cpu_value_now[$i]['value'];
        }
        $average_now = $sum / count($cpu_value_now);

        $cpu_average_two_days = $influxdb->query("SELECT mean(value) FROM cpu_value WHERE time>=now()-2d AND type_instance='idle' GROUP BY instance")->getPoints();
        $sum = 0;
        for ($i = 0; $i < count($cpu_average_two_days); $i++) {
            $sum += 100 - $cpu_average_two_days[$i]['mean'];
        }
        $average_two_days = $sum / count($cpu_average_two_days);


        $cpu_maximums = $influxdb->query("SELECT min(value) FROM cpu_value where time>=now()-2d AND type_instance='idle' GROUP BY instance")->getPoints();
        $maximum_two_days = 0;
        for ($i = 0; $i < count($cpu_maximums); $i++) {
            $sum = 0;
            $time = $cpu_maximums[$i]['time'];
            $cpu_max = $influxdb->query("SELECT value FROM cpu_value WHERE time>='" . $time . "' -2s AND time<='" . $time . "' +2s AND type_instance='idle' LIMIT 8")->getPoints();
            for ($j = 0; $j < count($cpu_max); $j++) {
                $sum += 100 - $cpu_max[$j]['value'];
            }
            $average = $sum / count($cpu_max);
            if ($average > $maximum_two_days) {
                $maximum_two_days = $average;
            }
        }
        $memory_now = $influxdb->query("SELECT value,type_instance from memory_value ORDER BY time DESC limit 6")->getPoints();
        $used_memory = 0;
        for ($i = 0; $i < count($memory_now); $i++) {
            $type = $memory_now[$i]['type_instance'];
            if ($type != 'slab_recl' && $type != 'slab_unrecl') {
                if ($type == 'used')
                    $used_memory = $memory_now[$i]['value'];
            }
        }
        $total_memory = 32 << 30;
        $max_used_two_days = $influxdb->query("SELECT MAX(value) FROM memory_value WHERE type_instance='used' AND time>=now()-2d")->getPoints();
        $max_used = $max_used_two_days[0]['max'];
        $disk_value = $influxdb->query("SELECT value,type_instance FROM df_value WHERE instance='root' ORDER by time DESC LIMIT 3")->getPoints();
        $used = 0;
        $free = 0;
        for ($i = 0; $i < count($disk_value); $i++) {
            $type = $disk_value[$i]['type_instance'];
            if ($type == 'free') {
                $free = $disk_value[$i]['value'];
            } else {
                $used += $disk_value[$i]['value'];
            }
        }
        $proc = array();        //array with name and value(enabled/disabled)
        $processes = $influxdb->query("SELECT value from processes_processes GROUP BY instance ORDER BY time DESC LIMIT 1")->getSeries();
        for ($i = 0; $i < count($processes); $i++) {
            $proc[$i]['name'] = $processes[$i]['tags']['instance'];
            $index = array_search("value", $processes[$i]['columns']);
            $proc[$i]['enabled'] = $processes[$i]['values'][0][$index] > 0 ? 1 : 0;
        }
        /*$apache_connections = $influxdb->query("SELECT value from apache_value where type='apache_connections' ORDER BY time DESC LIMIT 1")->getPoints()[0]['value'];
        $apache_max_connections = $influxdb->query("SELECT max(value) from apache_value where type='apache_connections' AND time>=now()-2d")->getPoints()[0]['max'];
       */ $stats = [
            "cpu" => [
                "average_now" => $average_now,
                "average_two_days" => $average_two_days,
                "maximum_two_days" => $maximum_two_days
            ],
            'memory' => [
                "used" => $this->toGB($used_memory),
                "free" => $this->toGB($total_memory - $used_memory),
                "total_memory" => $this->toGB($total_memory),
                "max_used_two_days" => $this->toGB($max_used)
            ],
            'disk' => [
                "used" => $this->toGB($used),
                "free" => $this->toGB($free)
            ],
            'processes' => $proc,
            /*'apache' => [
                'connections' => $apache_connections,
                'max_connections' => $apache_max_connections
            ]*/
        ];
       if ($stats['cpu']['average_now']>70)
       {
            Mail::to(env("ADMIN_EMAIL"))->queue(new NotifyCollectdMail("CPU average now >70%",$code));
       }
       if ($stats['cpu']['average_two_days']>60)
       {
           Mail::to(env("ADMIN_EMAIL"))->queue(new NotifyCollectdMail("CPU average two days >60%",$code));
       }
       /*if ($stats['cpu']['maximum_two_days']>70)
       {
           Mail::to(env("ADMIN_EMAIL"))->queue(new NotifyCollectdMail("CPU maximum two days >70%",$code));

       }*/
/*
       if ($stats['memory']['max_used_two_days']>24)
       {
           Mail::to(env("ADMIN_EMAIL"))->queue(new NotifyCollectdMail("Memory used>24GB",$code));

       }
  */      for ($i=0;$i<count($stats['processes']);$i++)
        {
            dump($stats['processes'][$i]);
            if ($stats['processes'][$i]['enabled']==0)
            {
                Mail::to(env("ADMIN_EMAIL"))->queue(new NotifyCollectdMail($stats['processes'][$i]['name']." disabled",$code));
            }
        }
        foreach (Coin::all() as $coin)
        {
            $rpcClient = new Client('http://localhost:' . $coin->port);
            $rpcClient->getHttpClient()
                ->withUsername($coin->user)
                ->withPassword($coin->pass);
            try {
                $b = $rpcClient->execute('getconnectioncount');
                if ($b > 0) {

                }
            } catch (\Exception $e) {
                report($e);
                Mail::to(env("ADMIN_EMAIL"))->queue(new NotifyCollectdMail($coin->name." disabled",$code));
            }
        }
    }
    protected function toGB($value)
    {
        return $value >> 30;
    }

}
