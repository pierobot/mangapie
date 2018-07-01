<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultCoverArchiveIdToManga extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manga', function (Blueprint $table) {
            $table->unsignedInteger('cover_archive_id')->references('id')->on('archives');
            $table->unsignedInteger('cover_archive_page')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manga', function (Blueprint $table) {
            $table->dropColumn('cover_archive_id');
            $table->dropColumn('cover_archive_page');
        });
    }
}
