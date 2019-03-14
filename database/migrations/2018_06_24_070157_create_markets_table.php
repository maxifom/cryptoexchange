<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::create('markets', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger("base_currency_id");
                $table->unsignedInteger("trade_currency_id");
                $table->decimal("low", 15, 8)->default(0);
                $table->decimal("high", 15, 8)->default(0);
                $table->decimal("open", 15, 8)->default(0);
                $table->decimal("close", 15, 8)->default(0);
                $table->unsignedInteger("trade_count_24hrs")->default(0);
                $table->decimal("volume_base", 15, 8)->default(0);
                $table->decimal("volume_trade", 15, 8)->default(0);
                $table->foreign('base_currency_id')->references('id')->on('coins')->onDelete('cascade');
                $table->foreign('trade_currency_id')->references('id')->on('coins')->onDelete('cascade');
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
            Schema::dropIfExists('markets');
        });
    }
}
