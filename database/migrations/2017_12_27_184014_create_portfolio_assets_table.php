<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePortfolioAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolio_assets', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('portfolio_id');
            $table->string('origin_id');
            $table->string('user_id');
            $table->string('name');
            $table->string('amount');
            $table->string('price');
            $table->string('balance');
            $table->string('counter_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portfolio_assets');
    }
}
