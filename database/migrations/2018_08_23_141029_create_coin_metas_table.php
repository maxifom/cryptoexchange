<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoinMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coin_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('coin_id');
            $table->string('source');
            $table->string('block_explorer')->nullable();
            $table->string('announcement')->nullable();
            $table->string('type',10)->default('PoW');
            $table->timestamps();
            $table->foreign('coin_id')->references('id')->on('coins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coin_metas');
    }
}
