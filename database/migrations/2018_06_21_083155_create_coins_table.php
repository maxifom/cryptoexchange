<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCoinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::create('coins', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 10);
                $table->string('user', 6)->default('user');
                $table->string('pass', 6)->default('pass');
                $table->integer('port')->unsigned();
                $table->integer('needed_confirmations')->unsigned()->default(3);
                $table->timestamps();
                $table->index('name');
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
            Schema::dropIfExists('coins');
        });

    }
}
