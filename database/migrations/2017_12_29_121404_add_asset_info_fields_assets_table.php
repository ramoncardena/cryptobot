<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssetInfoFieldsAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portfolio_assets', function(Blueprint $table){
            $table->dropColumn('name');
            $table->string('symbol');
            $table->string('full_name');
            $table->string('logo_url');
            $table->string('info_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function($table) {
            $table->string('name');
            $table->dropColumn('symbol');
            $table->dropColumn('full_name');
            $table->dropColumn('logo_url');
            $table->dropColumn('info_url');
        });
    }
}
