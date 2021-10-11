<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUablogsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uablogs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('SequenceNo');
            $table->text('reqBody');
            $table->text('resBody');
            $table->boolean('is_callback')->default(false);
            $table->string('charging_status')->nullable();
            $table->text('callback_body')->nullable();
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
        Schema::dropIfExists('uablogs');
    }
}
