<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReaderHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reader_histories', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->references('id')->on('users');
            $table->unsignedInteger('manga_id')->references('id')->on('manga');
            $table->text('archive_name')->required();
            $table->unsignedInteger('page')->required();
            $table->unsignedInteger('page_count')->required();

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
        Schema::dropIfExists('reader_histories');
    }
}
