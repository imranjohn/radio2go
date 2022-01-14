<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsBrandedStationColumnInBrandStations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brand_stations', function (Blueprint $table) {
            $table->boolean('is_branded_station')->default(true)->after('created_by'); //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brand_stations', function (Blueprint $table) {
            $table->dropColumn(['is_branded_station']);//
        });
    }
}
