<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
class TradeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::delete("DELETE FROM user_trades WHERE 1");
        DB::delete("DELETE FROM trades WHERE 1");
        DB::statement("ALTER TABLE trades AUTO_INCREMENT=0");
        factory(App\Trade::class, 100)->create();
        DB::update("UPDATE wallets SET balance=1000000 WHERE 1");
        //DB::delete("DELETE FROM market_histories WHERE 1");
        Artisan::call('cache:clear');
    }
}

