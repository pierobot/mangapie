<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\MangaInformation;

class MigrateMangaInformationToMangaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manga', function (Blueprint $table) {
            $table->unsignedInteger('mu_id')->nullable();
            $table->text('description')->nullable();
            $table->string('type', 20)->nullable();
            $table->string('year', 4)->nullable();
        });

        // migrate existing data from manga_information table to manga table
        $all = MangaInformation::all();
        if ($all->count()) {
            foreach ($all as $info) {
                $manga = $info->manga;
                if ($manga != null) {
                    $manga->update([
                        'mu_id' => $info->getMangaUpdatesId(),
                        'description' => $info->getDescription(),
                        'type' => $info->getType(),
                        'year' => $info->getYear()
                    ]);

                    $manga->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manga', function (Blueprint $table) {
            $table->dropColumn('mu_id');
            $table->dropColumn('description');
            $table->dropColumn('type');
            $table->dropColumn('year');
        });
    }
}
