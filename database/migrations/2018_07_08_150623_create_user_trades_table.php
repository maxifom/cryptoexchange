<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUserTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {

            Schema::create('user_trades', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('trade_id');
                $table->unsignedInteger('user_id_taker');
                $table->unsignedInteger('user_id_maker');
                $table->unsignedInteger('market_id');
                $table->decimal('amount', 15, 8)->default(0);
                $table->decimal('price', 15, 8)->default(0);
                $table->enum('type', array('buy', 'sell'));
                $table->foreign('trade_id')->references('id')->on('trades')->onDelete('cascade');
                $table->foreign('user_id_maker')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('user_id_taker')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('market_id')->references('id')->on('markets')->onDelete('cascade');
                $table->timestamps();
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

            Schema::dropIfExists('user_trades');
        });

    }
}
