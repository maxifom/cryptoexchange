<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiIpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_ips', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('api_id');
            $table->unsignedInteger('api_ip');
            $table->timestamps();
            $table->foreign('api_id')->references('id')->on('api_entries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_ips');
    }
}
