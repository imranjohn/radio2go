<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAudioAndAudioDurationColum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brand_stations', function (Blueprint $table) {
            $table->text('audio_url')->nullable()->after('image_url');
            $table->string('audio_duration')->nullable()->after('audio_url');
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
            $table->dropColumn(['audio_url', 'audio_duration']);
        });
    }
}
