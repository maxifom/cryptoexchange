<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::create('deposits', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('wallet_id');
                $table->string('tx', 64);
                $table->decimal('value', 15, 8)->default(0);
                $table->unsignedInteger('confirmations')->default(0);
                $table->unsignedInteger('n')->default(0);
                $table->timestamps();
                $table->timestamp("tx_time")->useCurrent();
                $table->boolean("confirmed")->default(false);
                $table->foreign("wallet_id")->references('id')->on('wallets')->onDelete('cascade');
                $table->unique(['tx','wallet_id','n'],'unique_tx_n');
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
            Schema::dropIfExists('deposits');
        });
    }
}
