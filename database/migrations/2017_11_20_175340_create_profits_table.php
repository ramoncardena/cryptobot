<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profits', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('trade_id');
            $table->string('order_id');
            $table->string('status');
            $table->string('exchange');
            $table->string('pair');
            $table->decimal('price', 20, 10);
            $table->decimal('amount', 20, 10);
            $table->string('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profits');
    }
}
