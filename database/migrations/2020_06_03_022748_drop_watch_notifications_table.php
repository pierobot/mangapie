<?php

use App\Archive;
use App\Manga;
use App\User;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Illuminate\Notifications\DatabaseNotification;

class DropWatchNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $notifications = \DB::table('watch_notifications')->get();

        $userIds = $notifications->pluck('user_id')->toArray();
        $seriesIds = $notifications->pluck('manga_id')->toArray();
        $archiveIds = $notifications->pluck('archive_id')->toArray();

        $users = User::query()
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');
        $series = Manga::query()
            ->whereIn('id', $seriesIds)
            ->get()
            ->keyBy('id');
        $archives = Archive::query()
            ->whereIn('id', $archiveIds)
            ->get()
            ->keyBy('id');

        foreach ($notifications as $notification) {
            /** @var User $user */
            $user = $users->get($notification->user_id);
            /** @var Manga $singleSeries */
            $singleSeries = $series->get($notification->manga_id);
            /** @var Archive $archive */
            $archive = $archives->get($notification->archive_id);

            $data = [
                'series' => [
                    'id' => $singleSeries->id,
                    'name' => $singleSeries->name
                ],

                'archive' => [
                    'id' => $archive->id,
                    'name' => $archive->name
                ]
            ];

            DatabaseNotification::query()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => \App\Notifications\NewArchiveNotification::class,
                'notifiable_id' => $user->id,
                'notifiable_type' => User::class,
                'data' => $data
            ]);
        }

        Schema::drop('watch_notifications');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('watch_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('manga_id');
            $table->unsignedInteger('archive_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('manga_id')->references('id')->on('manga')->onDelete('cascade');
            $table->foreign('archive_id')->references('id')->on('archives')->onDelete('cascade');

            $table->index(['user_id', 'manga_id', 'archive_id']);

            $table->timestamps();
        });
    }
}
