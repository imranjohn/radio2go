<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoColumnInBrandStatsions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brand_stations', function (Blueprint $table) {
            $table->text('video_url')->nullable()->after('image_url');
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
            $table->dropColumn(['video_url']);
        });
    }
}
