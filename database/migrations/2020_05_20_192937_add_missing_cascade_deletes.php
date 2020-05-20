<?php

use App\Archive;
use App\ArchiveView;
use App\ArtistReference;
use App\AssociatedNameReference;
use App\Comment;
use App\Favorite;
use App\Manga;
use App\MangaView;
use App\Vote;
use App\WatchNotification;
use App\WatchReference;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingCascadeDeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ArchiveView::whereDoesntHave('user')
            ->orWhereDoesntHave('archive')
            ->forceDelete();

        Schema::table('archive_views', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
            $table->foreign('archive_id')->references('id')->on('archives')->onDelete('cascade')->change();

            $table->index(['archive_id']);
        });

        Archive::whereDoesntHave('manga')->forceDelete();

        Schema::table('archives', function (Blueprint $table) {
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade')->change();

            $table->index(['manga_id']);
        });

        Comment::whereDoesntHave('manga')
            ->orWhereDoesntHave('user')
            ->forceDelete();

        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade')->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();

            // This was originally to allow for archive comments, but this will be done differently in the future
            $table->dropColumn('archive_id');

            $table->index(['manga_id', 'user_id']);
        });

        Favorite::whereDoesntHave('manga')
            ->orWhereDoesntHave('user')
            ->forceDelete();

        Schema::table('favorites', function (Blueprint $table) {
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade')->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();

            $table->index(['manga_id', 'user_id']);
        });

        Manga::whereDoesntHave('library')->forceDelete();

        Schema::table('manga', function (Blueprint $table) {
            $table->foreign('library_id')->references('id')->on('libraries')->onDelete('cascade')->change();

            $table->index(['library_id']);
        });

        MangaView::whereDoesntHave('manga')
            ->orWhereDoesntHave('user')
            ->forceDelete();

        Schema::table('manga_views', function (Blueprint $table) {
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade')->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();

            $table->index(['manga_id']);
        });

        Vote::whereDoesntHave('manga')
            ->orWhereDoesntHave('user')
            ->forceDelete();

        Schema::table('votes', function (Blueprint $table) {
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade')->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();

            $table->index(['manga_id']);
        });

        WatchNotification::whereDoesntHave('manga')
            ->orWhereDoesntHave('user')
            ->orWhereDoesntHave('archive')
            ->forceDelete();

        Schema::table('watch_notifications', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade')->change();
            $table->foreign('archive_id')->references('id')->on('archives')->onDelete('cascade')->change();

            $table->index(['user_id', 'manga_id', 'archive_id']);
        });

        WatchReference::whereDoesntHave('manga')
            ->orWhereDoesntHave('user')
            ->forceDelete();

        Schema::table('watch_references', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade')->change();

            $table->index(['user_id', 'manga_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('archive_views', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['archive_id']);

            $table->dropIndex(['archive_id']);
        });

        Schema::table('archives', function (Blueprint $table) {
            $table->dropForeign(['manga_id']);

            $table->dropIndex(['manga_id']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['manga_id']);
            $table->dropForeign(['user_id']);

            $table->unsignedInteger('archive_id')->nullable()->default(0);

            $table->dropIndex(['manga_id', 'user_id']);
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropForeign(['manga_id']);
            $table->dropForeign(['user_id']);

            $table->dropIndex(['manga_id', 'user_id']);
        });

        Schema::table('manga', function (Blueprint $table) {
            $table->dropForeign(['library_id']);

            $table->dropIndex(['library_id']);
        });

        Schema::table('manga_views', function (Blueprint $table) {
            $table->dropForeign(['manga_id']);
            $table->dropForeign(['user_id']);

            $table->dropIndex(['manga_id']);
        });

        Schema::table('votes', function (Blueprint $table) {
            $table->dropForeign(['manga_id']);
            $table->dropForeign(['user_id']);

            $table->dropIndex(['manga_id']);
        });

        Schema::table('watch_notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['manga_id']);
            $table->dropForeign(['archive_id']);

            $table->dropIndex(['user_id', 'manga_id', 'archive_id']);
        });

        Schema::table('watch_references', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['manga_id']);

            $table->dropIndex(['user_id', 'manga_id']);
        });
    }
}
