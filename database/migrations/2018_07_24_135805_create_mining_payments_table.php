<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMiningPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::create('mining_payments', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('coin_id');
                $table->decimal('amount', 15, 8);
                $table->unsignedInteger('user_id')->nullable();
                $table->string('tx', 100);
                $table->timestamps();
                $table->foreign('coin_id')->references('id')->on('coins')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
            Schema::dropIfExists('mining_payments');
        });
    }
}
