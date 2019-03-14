<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawalConfirmationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawal_confirmations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('withdrawal_id');
            $table->string('token',32)->unique();
            $table->timestamps();
            $table->foreign('withdrawal_id')->references('id')->on('withdrawals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdrawal_confirmations');
    }
}
