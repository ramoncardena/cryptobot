<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConditionsAndClosingPriceToTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trades', function(Blueprint $table){
            $table->string('condition_id');
            $table->string('condition');
            $table->decimal('condition_price', 20, 10);
            $table->decimal('closing_price', 20, 10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trades', function($table) {
             $table->dropColumn('condition_id');
             $table->dropColumn('condition');
             $table->dropColumn('condition_price');
             $table->dropColumn('closing_price');
          });
    }
}
