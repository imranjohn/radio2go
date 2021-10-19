<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArtworkImageInStations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->text('artwork_image')->nullable()->after('image_url');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->dropColumn('artwork_image');
        });
    }
}
