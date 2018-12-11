<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReplaceArchiveNameWithId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add the column so we have something to migrate the existing histories into
        Schema::table('reader_histories', function (Blueprint $table) {
            $table->unsignedInteger('archive_id')->references('id')->on('archives');
        });

        $histories = \App\ReaderHistory::all();
        // migrate
        foreach ($histories as $history) {
            $archive = \App\Archive::where('name', $history->archive_name)->first();

            // if the archive doesn't exist just delete the history for it
            if (empty($archive)) {
                $history->forceDelete();

                continue;
            }

            $history->update([
                'archive_id' => $archive->id
            ]);
        }

        // data migration has been taken care of and we can now drop the archive_name column
        Schema::table('reader_histories', function (Blueprint $table) {
            $table->dropColumn('archive_name');
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
            $table->dropColumn('archive_id');

            $table->text('archive_name');
        });
    }
}
