<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->increments('id');

            $table->text('name');
            $table->unsignedInteger('mu_id')->nullable();

            $table->timestamps();
        });

        // migrate the authors to the people table
        $authors = DB::select('select name, mu_id from authors');
        foreach ($authors as $author) {
            \App\Person::updateOrCreate([
                'name' => $author->name,
                'mu_id' => $author->mu_id,
            ]);
        }

        // migrate the artists to the people table
        $artists = DB::select('select name, mu_id from artists');
        foreach ($artists as $artist) {
            \App\Person::updateOrCreate([
                'name' => $artist->name,
                'mu_id' => $artist->mu_id
            ]);
        }

        // update the ids of the author_references table
        $authorReferences = \App\AuthorReference::all();
        foreach ($authorReferences as $authorReference) {
            $author = DB::selectOne('select name from authors where id = ?', [$authorReference->author_id]);
            $person = \App\Person::where('name', $author->name)->first();

            $authorReference->update([
                'author_id' => $person->id,
            ]);
        }

        // update the ids of the artist_references table
        $artistReferences = \App\ArtistReference::all();
        foreach ($artistReferences as $artistReference) {
            $artist = DB::selectOne('select name from artists where id = ?', [$artistReference->artist_id]);
            $person = \App\Person::where('name', $artist->name)->first();

            $artistReference->update([
                'artist_id' => $person->id
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
}
