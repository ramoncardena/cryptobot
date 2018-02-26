<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsTickersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickers', function(Blueprint $table){
            $table->string('coinname')->nullable($value = true);      
            $table->string('fullname')->nullable($value = true);
            $table->string('imageurl')->nullable($value = true);
            $table->string('url')->nullable($value = true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickers', function($table) {
            $table->dropColumn('coinname');
            $table->dropColumn('fullname');
            $table->dropColumn('imageurl');
            $table->dropColumn('url');
        });
    }
}
