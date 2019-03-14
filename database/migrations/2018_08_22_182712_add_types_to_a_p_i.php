<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypesToAPI extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_entries', function (Blueprint $table) {
            $table->boolean('trade')->default(0);
            $table->boolean('wallet')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_entries', function (Blueprint $table) {
            $table->dropColumn('trade');
            $table->dropColumn('wallet');
        });
    }
}
