<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use \App\Manga;
use \App\MangaInformation;

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
            $table->string('type', 20)->nullable();
            $table->text('description')->nullable();
            $table->string('year', 4)->nullable();
        });

        // migrate data from manga_information to manga table
        $information = MangaInformation::all();
        if ($information->count() > 0) {
            foreach ($information as $info) {
                $id = $info->getMangaId();
                $manga = Manga::find($id);
                if ($manga != null) {
                    $manga->update([
                        'mu_id' => $info->getMangaUpdatesId(),
                        'type' => $info->getType(),
                        'description' => $info->getDescription(),
                        'year' => $info->getYear()
                    ]);

                    $manga->save();
                }
            }
        }

        Schema::drop('manga_information');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('manga_information', function (Blueprint $table) {
            $table->unsignedInteger('id')->references('id')->on('manga');
            $table->primary('id');

            // nullable in case user decides not to use mangaupdates
            $table->unsignedInteger('mu_id')->nullable();
            $table->text('name')->nullable();
            $table->text('description')->nullable();
            $table->string('type', 20)->nullable();
            $table->string('year', 4)->nullable();

            $table->timestamps();
        });

        // migrate
        $manga = Manga::all();
        if ($manga != null) {
            $info = MangaInformation::forceCreate([
                'id' => $manga->getId(),
                'mu_id' => $manga->getMangaUpdatesId(),
                'name' => $manga->getName(),
                'description' => $manga->getDescription(),
                'type' => $manga->getType(),
                'year' => $manga->getYear()
            ]);

            $info->save();
        }

        Schema::table('manga', function (Blueprint $table) {
            $table->dropColumn('mu_id');
            $table->dropColumn('type');
            $table->dropColumn('description');
            $table->dropColumn('year');
        });
    }
}
