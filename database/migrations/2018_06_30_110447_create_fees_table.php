<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::create('fees', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('coin_id');
                $table->decimal('fee', 15, 8)->default(0);
                $table->timestamps();
                $table->foreign("coin_id")->references('id')->on('coins')->onDelete('cascade');
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
            Schema::dropIfExists('fees');
        });
    }
}
