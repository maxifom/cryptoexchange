<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMarketHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::create('market_histories', function (Blueprint $table) {
                $table->increments('id');
                $table->decimal('open', 15, 8)->default(0);
                $table->decimal('high', 15, 8)->default(0);
                $table->decimal('low', 15, 8)->default(0);
                $table->decimal('close', 15, 8)->default(0);
                $table->decimal('volume', 15, 8)->default(0);
                $table->decimal('volume_base', 15, 8)->default(0);
                $table->unsignedInteger('trade_count')->default(0);
                $table->unsignedInteger('market_id');
                $table->timestamp('market_time')->useCurrent();
                $table->timestamps();
                $table->foreign('market_id')->references('id')->on('markets')->onDelete('cascade');
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
            Schema::dropIfExists('market_histories');
        });
    }
}
