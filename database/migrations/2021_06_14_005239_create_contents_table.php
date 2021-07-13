<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cid');
            $table->string('name');
            $table->string('display_name');
            $table->tinyInteger('content_type')->unsigned();
            $table->string('file_name');
            $table->string('file_type');
            $table->string('file_size')->nullable();
            $table->string('display_time')->nullable();
            $table->tinyInteger('delete_flag')->default(0)->unsigned();
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
        Schema::dropIfExists('contents');
    }
}
