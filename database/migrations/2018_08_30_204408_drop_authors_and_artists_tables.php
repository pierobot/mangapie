<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAuthorsAndArtistsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('authors');
        Schema::dropIfExists('artists');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // create the tables again - though they'll technically be useless; and empty
        $authorsMigration = new CreateAuthorsTable();
        $authorsMigration->up();

        $artistsMigration = new CreateArtistsTable();
        $artistsMigration->up();
    }
}
