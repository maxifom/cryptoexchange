<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {

            Schema::create('withdrawals', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('wallet_id');
                $table->decimal('value', 15, 8)->default(0);
                $table->string('address', 100);
                $table->string('tx', 64)->nullable();
                $table->enum('status', ['requested', 'approved', 'sent']);
                $table->timestamps();
                $table->foreign("wallet_id")->references('id')->on('wallets')->onDelete('cascade');
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
            Schema::dropIfExists('withdrawals');
        });

    }
}
