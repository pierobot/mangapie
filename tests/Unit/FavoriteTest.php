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

        $response = $this->actingAs($user)
            ->withSession(['foo' => 'bar'])
            ->followingRedirects()
            ->post(\URL::action('FavoriteController@update'), [
                'id' => $manga->getId(),
                'action' => 'favorite'
            ]);

        $response->assertSeeText('You have favorited this manga.');
    }

    public function testUserUnfavorite()
    {
        $library = factory(Library::class)->create();
        $user = factory(User::class)->create();
        $manga = factory(Manga::class)->create([
            'library_id' => $library->getId()
        ]);

        $response = $this->actingAs($user)
            ->withSession(['foo' => 'bar'])
            ->followingRedirects()
            ->post(\URL::action('FavoriteController@update'), [
                'id' => $manga->getId(),
                'action' => 'unfavorite'
            ]);

        $response->assertSeeText('You have unfavorited this manga.');
    }

    public function testSeeFavoritedManga()
    {
        $favorite = factory(Favorite::class)->create();
        $user = User::find($favorite->user->getId());

        $response = $this->actingAs($user)
                         ->withSession(['foo' => 'bar'])
                         ->get(\URL::action('FavoriteController@index'));

        $response->assertViewIs('manga.favorites');
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

        $response->assertViewIs('manga.favorites');
        $response->assertSeeText('Favorites: (0)');
        $response->assertDontSeeText($favorite->manga->getName());
    }
}
