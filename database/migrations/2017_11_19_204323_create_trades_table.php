<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('user_id');
            $table->string('order_id');
            $table->string('stop_id');
            $table->string('profit_id');
            $table->string('status');
            $table->string('position');
            $table->string('exchange');
            $table->string('pair');
            $table->decimal('price', 20, 10);
            $table->decimal('amount', 20, 10);
            $table->decimal('total', 20, 10);
            $table->decimal('profit', 20, 10);
            $table->decimal('stop_loss', 20, 10);
            $table->decimal('take_profit', 20, 10);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trades');
    }
}
