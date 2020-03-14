<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysAndIndicesToReferenceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('associated_name_references', function (Blueprint $table) {
            $table->renameColumn('assoc_name_id', 'associated_name_id')->change();

            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade');
            $table->foreign('associated_name_id')->references('id')->on('associated_names')->onDelete('cascade');

            $table->index(['manga_id', 'associated_name_id']);
        });

        Schema::table('genre_references', function (Blueprint $table) {
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade');
            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');

            $table->index(['manga_id', 'genre_id']);
        });

        Schema::table('artist_references', function (Blueprint $table) {
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade');
            $table->foreign('artist_id')->references('id')->on('people')->onDelete('cascade');

            $table->index(['manga_id', 'artist_id']);
        });

        Schema::table('author_references', function (Blueprint $table) {
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('people')->onDelete('cascade');

            $table->index(['manga_id', 'author_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*
         * Specify the foreign keys in an array because... ?.
         *
         * "// If the given "index" is actually an array of columns, the developer means
            // to drop an index merely by specifying the columns involved without the
            // conventional name, so we will build the index name from the columns."
         */
        Schema::table('author_references', function (Blueprint $table) {
            $table->dropForeign(['manga_id']);
            $table->dropForeign(['author_id']);

            $table->dropIndex(['manga_id', 'author_id']);
        });

        Schema::table('artist_references', function (Blueprint $table) {
            $table->dropForeign(['manga_id']);
            $table->dropForeign(['artist_id']);

            $table->dropIndex(['manga_id', 'artist_id']);
        });

        Schema::table('genre_references', function (Blueprint $table) {
            $table->dropForeign(['manga_id']);
            $table->dropForeign(['genre_id']);

            $table->dropIndex(['manga_id', 'genre_id']);
        });

        Schema::table('associated_name_references', function (Blueprint $table) {
            $table->dropForeign(['manga_id']);
            $table->dropForeign(['associated_name_id']);

            $table->dropIndex(['manga_id', 'associated_name_id']);

            $table->renameColumn('associated_name_id', 'assoc_name_id')->change();
        });
    }
}
