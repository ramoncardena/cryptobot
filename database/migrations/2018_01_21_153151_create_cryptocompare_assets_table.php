<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCryptocompareAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cryptocompare_assets', function (Blueprint $table) {
            $table->timestamps();
            $table->string('id');
            $table->string('url');
            $table->string('imageurl');
            $table->string('name');
            $table->string('symbol');
            $table->string('coinname');
            $table->string('fullname');
            $table->string('algorithm');
            $table->string('prooftype');
            $table->string('fullypremined');
            $table->string('totalcoinsupply');
            $table->string('preminedvalue');
            $table->string('totalcoinsfreefloat');
            $table->string('sortorder');
            $table->string('sponsored');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cryptocompare_assets');
    }
}
