<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoinFundingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coin_fundings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('coin_id');
            $table->decimal('amount',15,8)->default(0);
            $table->decimal('needed_amount',15,8);
            $table->unsignedInteger('needed_confirmations')->default(6);
            $table->string('address',100);
            $table->boolean('funded')->default(0);
            $table->unsignedInteger('funding_coin_id');
            $table->timestamps();
            $table->foreign('coin_id')->references('id')->on('coins')->onDelete('cascade');
            $table->foreign('funding_coin_id','fund_c_id')->references('id')->on('coins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coin_fundings');
    }
}
