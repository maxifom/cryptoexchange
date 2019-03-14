<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreteTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {

            Schema::create('trades', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger("user_id");
                $table->unsignedInteger("market_id");
                $table->enum("type", ["buy", "sell"]);
                $table->decimal("amount", 15, 8);
                $table->decimal("price", 15, 8);
                $table->boolean("finished")->default(false);
                $table->unsignedInteger("user_id_taker")->nullable();
                $table->timestamps();
                $table->foreign('user_id_taker')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign("market_id")->references('id')->on('markets')->onDelete('cascade');
                $table->index('type');
                $table->index('price');
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
            Schema::dropIfExists('trades');
        });

    }
}
