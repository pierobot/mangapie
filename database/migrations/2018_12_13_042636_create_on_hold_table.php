<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOnHoldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('on_hold', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->references('id')->on('users');
            $table->unsignedInteger('manga_id')->references('id')->on('manga');

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
        Schema::dropIfExists('on_hold');
    }
}
