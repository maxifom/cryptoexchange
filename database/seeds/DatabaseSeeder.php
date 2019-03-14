<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //DB::statement("ALTER TABLE trades AUTO_INCREMENT=0");
        DB::transaction(function () {
            //DB::statement("TRUNCATE TABLE user_trades");
            DB::delete("DELETE FROM trades WHERE 1");
            factory(App\Trade::class, 100)->create();
            DB::update("UPDATE wallets SET balance=1000000 WHERE 1");
            Artisan::call('cache:clear');
            //DB::delete("DELETE FROM market_histories WHERE 1");
        },1);

    }
}
