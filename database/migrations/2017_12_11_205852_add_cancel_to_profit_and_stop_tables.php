<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancelToProfitAndStopTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profits', function(Blueprint $table){
            $table->boolean('cancel');
        });

        Schema::table('stops', function(Blueprint $table){
            $table->boolean('cancel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profits', function($table) {
            $table->dropColumn('cancel');
        });

        Schema::table('stops', function($table) {
            $table->dropColumn('cancel');
        });
    }
}
