<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::create('wallets', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('coin_id');
                $table->unsignedInteger('user_id');
                $table->string('address', 100)->nullable();
                $table->decimal('balance', 15, 8)->unsigned()->default(0);
                $table->timestamps();
                $table->foreign('coin_id')->references('id')->on('coins')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['coin_id','user_id']);
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
            Schema::dropIfExists('wallets');
        });
    }
}
