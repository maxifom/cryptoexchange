<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCoinInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::create('coin_infos', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('coin_id');
                $table->unsignedInteger('block_count');
                $table->string('last_block', 64);
                $table->timestamps();
                $table->timestamp('block_time')->useCurrent();
                $table->unsignedInteger('connections')->default(0);
                $table->foreign('coin_id')->references('id')->on('coins')->onDelete('cascade');
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
            Schema::dropIfExists('coin_infos');
        });
    }
}
