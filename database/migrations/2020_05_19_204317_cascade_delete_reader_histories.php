<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CascadeDeleteReaderHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reader_histories', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
            $table->foreign('archive_id')->references('id')->on('archives')->onDelete('cascade')->change();
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade')->change();

            $table->index(['user_id', 'archive_id', 'manga_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reader_histories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['archive_id']);
            $table->dropForeign(['manga_id']);

            $table->dropIndex(['user_id', 'archive_id', 'manga_id']);
        });
    }
}
