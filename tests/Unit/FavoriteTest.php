<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Favorite;
use App\Library;
use App\Manga;
use App\User;

/**
 * @covers \App\Favorite
 * @covers \App\Http\Controllers\FavoriteController
 * @covers \App\Http\Requests\FavoriteAddRequest
 * @covers \App\Http\Requests\FavoriteRemoveRequest
 */
class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->app->bind(\App\Observers\MangaObserver::class, function () {
            return $this->getMockBuilder(\App\Observers\MangaObserver::class)->disableOriginalConstructor()->getMock();
        });
    }

    public function testUserFavorite()
    {
        $library = factory(Library::class)->create();
        $user = factory(User::class)->create();
        $manga = factory(Manga::class)->create([
            'library_id' => $library->getId()
        ]);

        $this->actingAs($user)
            ->withSession(['foo' => 'bar'])
            ->followingRedirects()
            ->post(\URL::action('FavoriteController@create'), [
                'manga_id' => $manga->getId()
            ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'manga_id' => $manga->id
        ]);
    }

    public function testUserUnfavorite()
    {
        $library = factory(Library::class)->create();
        $user = factory(User::class)->create();
        $manga = factory(Manga::class)->create([
            'library_id' => $library->getId()
        ]);

        $this->actingAs($user)
            ->withSession(['foo' => 'bar'])
            ->followingRedirects()
            ->delete(\URL::action('FavoriteController@delete'), [
                'favorite_id' => $manga->getId(),
            ]);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'manga_id' => $manga->id
        ]);
    }

    public function testSeeFavoritedManga()
    {
        $favorite = factory(Favorite::class)->create();
        $user = User::find($favorite->user->getId());

        $response = $this->actingAs($user)
                         ->withSession(['foo' => 'bar'])
                         ->get(\URL::action('FavoriteController@index'));

        $response->assertViewIs('favorites.index');
        $response->assertSeeText('Favorites: (1)');
        $response->assertSeeText($favorite->manga->getName());
    }

    public function testDontSeeUnfavoritedManga()
    {
        $favorite = factory(Favorite::class)->create();

        Favorite::where('user_id', $favorite->user->getId())
                ->where('manga_id', $favorite->manga->getId())
                ->forceDelete();

        $user = User::find($favorite->user->getId());

        $response = $this->actingAs($user)
                         ->withSession(['foo' => 'bar'])
                         ->get(\URL::action('FavoriteController@index'));

        $response->assertViewIs('favorites.index');
        $response->assertSeeText('Favorites: (0)');
        $response->assertDontSeeText($favorite->manga->getName());
    }
}
