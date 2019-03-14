<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawalProfitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawal_profits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('trade_id');
            $table->boolean('trade_confirmed')->default(false);
            $table->decimal('value',15,8)->default(0);
            $table->enum('trade_type',['maker','taker'])->default('maker');
            $table->unsignedInteger('coin_id');
            $table->timestamps();
            $table->foreign('trade_id')->references('id')->on('trades')->onDelete('cascade');
            $table->foreign('coin_id')->references('id')->on('coins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdrawal_profits');
    }
}
