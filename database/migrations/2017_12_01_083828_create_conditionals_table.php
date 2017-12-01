<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConditionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conditionals', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('user_id');
            $table->string('trade_id');
            $table->string('exchange');
            $table->string('pair');
            $table->string('condition');
            $table->decimal('condition_price', 20, 10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conditionals');
    }
}
