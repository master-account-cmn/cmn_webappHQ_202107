<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointmapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pointmap', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('place_name');
            $table->double('lat');
            $table->double('lng');
            $table->tinyInteger('ride_flag')->default(0)->unsigned();
            $table->time('ride_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pointmap');
    }
}
