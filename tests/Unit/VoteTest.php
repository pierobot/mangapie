<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Library;
use App\Manga;
use App\User;
use App\Vote;

/**
 * @requires curl
 *
 * @covers \App\Vote
 * @covers \App\Rating
 * @covers \App\Http\Controllers\VoteController
 * @covers \App\Http\Requests\VoteCreateRequest
 * @covers \App\Http\Requests\VotePatchRequest
 * @covers \App\Http\Requests\VoteDeleteRequest
 */
class VoteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Asserts that creating a vote works as intended.
     *
     * @param $rating
     *
     * @testWith [3]
     */
    public function testCreateVote($rating)
    {
        $library = factory(Library::class)->create();
        $user = factory(User::class)->create();
        $manga = factory(Manga::class)->create([
            'library_id' => $library->getId()
        ]);

        $response =  $this->actingAs($user)
//            ->from(\URL::action('MangaController@index', [$manga]))
            ->followingRedirects()
            ->put(\URL::action('VoteController@put', [
                'manga_id' => $manga->id,
                'rating' => $rating
            ]));

        $this->assertDatabaseHas('votes', [
            'manga_id' => $manga->id,
            'user_id' => $user->id,
            'rating' => $rating
        ]);

        $response->assertSuccessful();
    }

    /**
     * Asserts that changing a vote's rating works as intended.
     *
     * @param $rating
     *
     * @testWith [5, 2]
     */
    public function testChangeVote($initialRating, $newRating)
    {
        $vote = factory(Vote::class)->create([
            'rating' => $initialRating
        ]);
        $user = $vote->user;
        $manga = $vote->manga;

        $this->assertDatabaseHas('votes', [
            'manga_id' => $manga->id,
            'user_id' => $user->id,
            'rating' => $initialRating
        ]);

        $response = $this->actingAs($user)
            ->from(\URL::action('MangaController@index', [$manga]))
            ->followingRedirects()
            ->put(\URL::action('VoteController@put', [
                'manga_id' => $manga->id,
                'rating' => $newRating
            ]));

        $this->assertDatabaseHas('votes', [
            'manga_id' => $manga->id,
            'user_id' => $user->id,
            'rating' => $newRating
        ]);

        $response->assertSuccessful();
    }

    /**
     * Asserts that deleting a vote works as intended.
     */
    public function testDeleteVote()
    {
        $vote = factory(Vote::class)->create();
        $user = $vote->user;
        $manga = $vote->manga;

        $response = $this->actingAs($user)
            ->from(\URL::action('MangaController@index', [$manga]))
            ->followingRedirects()
            ->delete(\URL::action('VoteController@delete', [
                'vote_id' => $vote->id
            ]));

        $this->assertDatabaseMissing('votes', [
            'manga_id' => $manga->id,
            'user_id' => $user->id
        ]);

        $response->assertSuccessful();
        $response->assertSee('Your vote was successfully deleted.');
    }
}
