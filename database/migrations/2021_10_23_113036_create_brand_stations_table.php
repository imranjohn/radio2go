<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_stations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('stream_url', 200)->nullable();
            $table->text('image_url')->nullable();
            $table->text('artwork_image')->nullable();
            $table->text('description')->nullable();
            $table->text('long_description')->nullable();
            $table->text('deep_link')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand_stations');
    }
}
