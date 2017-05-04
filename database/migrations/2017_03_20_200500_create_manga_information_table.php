<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMangaInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manga_information', function (Blueprint $table) {
            $table->unsignedInteger('id')->references('id')->on('manga');
            $table->primary('id');

            // nullable in case user decides not to use mangaupdates
            $table->unsignedInteger('mu_id')->nullable();
            $table->text('name')->nullable();
            $table->text('description')->nullable();
            $table->string('type', 20)->nullable();
            $table->string('year', 4)->nullable();

            $table->timestamps();
        });

        // Schema::table('manga_information', function (Blueprint $table) {

        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manga_information');
    }
}
