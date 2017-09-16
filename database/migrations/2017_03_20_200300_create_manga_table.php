<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMangaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manga', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // required? :thinking:
            $table->increments('id');

            $table->unsignedInteger('library_id')->references('id')->on('libraries');

            $table->text('name');
            $table->text('path');

            $table->timestamps();
        });

        DB::statement('CREATE FULLTEXT INDEX name_index ON manga(name);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manga', function($table) {
            $table->dropIndex('name_index');
        });

        Schema::dropIfExists('manga');
    }
}
