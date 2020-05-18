<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use \App\Completed;
use \App\Dropped;
use \App\Reading;
use \App\OnHold;
use \App\Planned;

class AddForeignKeysToLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('completed', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade')->change();

            $table->index(['user_id', 'manga_id']);
        });

        Schema::table('dropped', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade')->change();

            $table->index(['user_id', 'manga_id']);
        });

        Schema::table('reading', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade')->change();

            $table->index(['user_id', 'manga_id']);
        });

        Schema::table('on_hold', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade')->change();

            $table->index(['user_id', 'manga_id']);
        });

        Schema::table('planned', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade')->change();

            $table->index(['user_id', 'manga_id']);
        });

        // Purge entries that are stale
        Completed::whereDoesntHave('manga')
            ->orWhereDoesntHave('user')
            ->forceDelete();

        Dropped::whereDoesntHave('manga')
            ->orWhereDoesntHave('user')
            ->forceDelete();

        Reading::whereDoesntHave('manga')
            ->orWhereDoesntHave('user')
            ->forceDelete();

        OnHold::whereDoesntHave('manga')
            ->orWhereDoesntHave('user')
            ->forceDelete();

        Planned::whereDoesntHave('manga')
            ->orWhereDoesntHave('user')
            ->forceDelete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('completed', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['manga_id']);

            $table->dropIndex(['user_id', 'manga_id']);
        });

        Schema::table('dropped', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['manga_id']);

            $table->dropIndex(['user_id', 'manga_id']);
        });

        Schema::table('reading', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['manga_id']);

            $table->dropIndex(['user_id', 'manga_id']);
        });

        Schema::table('on_hold', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['manga_id']);

            $table->dropIndex(['user_id', 'manga_id']);
        });

        Schema::table('planned', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['manga_id']);

            $table->dropIndex(['user_id', 'manga_id']);
        });
    }
}
