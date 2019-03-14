<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoinFundingDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coin_funding_deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('funding_id');
            $table->string('tx',64);
            $table->decimal('value',15,8)->default(0);
            $table->unsignedInteger('confirmations')->default(0);
            $table->boolean('confirmed')->default(0);
            $table->timestamps();
            $table->foreign('funding_id')->references('id')->on('coin_fundings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coin_funding_deposits');
    }
}
