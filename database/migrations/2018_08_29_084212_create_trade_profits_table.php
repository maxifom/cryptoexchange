<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTradeProfitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_profits', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('value',15,8)->default(0);
            $table->unsignedInteger('coin_id');
            $table->unsignedInteger('w_id');
            $table->timestamps();
            $table->foreign('coin_id')->references('id')->on('coins')->onDelete('cascade');
            $table->foreign('w_id')->references('id')->on('withdrawals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade_profits');
    }
}
