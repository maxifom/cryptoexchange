<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class AddCoinbaseMaturityToCoins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {

            Schema::table('coins', function (Blueprint $table) {
                $table->unsignedInteger('coinbase_maturity')->default(20);
            });
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::transaction(function () {

            Schema::table('coins', function (Blueprint $table) {
                $table->dropColumn('coinbase_maturity');
            });
        });

    }
}
