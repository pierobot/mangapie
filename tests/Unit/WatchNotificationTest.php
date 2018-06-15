<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Archive;
use App\Library;
use App\Manga;
use App\User;
use App\WatchNotification;
use App\WatchReference;

/**
 * @covers \App\User
 * @covers \App\Library
 * @covers \App\Manga
 * @covers \App\WatchReference
 * @covers \App\WatchNotification
 */
class WatchNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->app->bind(\App\Observers\MangaObserver::class, function () {
            return $this->getMockBuilder(\App\Observers\MangaObserver::class)->disableOriginalConstructor()->getMock();
        });
    }

    public function testUserBeginWatching()
    {
        $library = factory(Library::class)->create();
        $user = factory(User::class)->create();
        $manga = factory(Manga::class)->create([
            'library_id' => $library->getId()
        ]);

        $response = $this->actingAs($user)
                         ->withSession(['foo' => 'bar'])
                         ->followingRedirects()
                         ->post(\URL::action('WatchController@update'), [
                             'id' => $manga->getId(),
                             'action' => 'watch'
                         ]);

        $response->assertSeeText('You are now watching this manga.');
    }

    public function testUserStopWatching()
    {
        $library = factory(Library::class)->create();
        $user = factory(User::class)->create();
        $manga = factory(Manga::class)->create([
            'library_id' => $library->getId()
        ]);

        $response = $this->actingAs($user)
                         ->withSession(['foo' => 'bar'])
                         ->followingRedirects()
                         ->post(\URL::action('WatchController@update'), [
                             'id' => $manga->getId(),
                             'action' => 'unwatch'
                         ]);

        $response->assertSeeText('You are no longer watching this manga.');
    }

    public function testUserReceivesNotificationIfWatching()
    {
        $watchReference = factory(WatchReference::class)->create();
        $user = $watchReference->user;
        $manga = $watchReference->manga;

        $archive = factory(Archive::class)->create([
            'manga_id' => $manga->getId()
        ]);

        $user->load('watchNotifications');

        $response = $this->actingAs($user)
                         ->withSession(['foo' => 'bar'])
                         ->get(\URL::action('NotificationController@index'));

        $user->load('watchNotifications');

        $response->assertViewIs('notifications.index');
        $response->assertSee("<span class=\"badge\" id=\"notification-count\">1</span>");
        $response->assertSeeText("Notifications (1)");
        $response->assertSeeText($manga->getName());
    }

    public function testUserDoesNotReceiveNotificationIfUnwatched()
    {
        $watchReference = factory(WatchReference::class)->create();
        $user = $watchReference->user;
        $manga = $watchReference->manga;

        $watchReference->forceDelete();

        $archive = factory(Archive::class)->create([
            'manga_id' => $manga->getId()
        ]);

        $user->load('watchNotifications');

        $response = $this->actingAs($user)
                         ->withSession(['foo' => 'bar'])
                         ->get(\URL::action('NotificationController@index'));

        $response->assertViewIs('notifications.index');
        $response->assertDontSee("<span class=\"badge\" id=\"notification-count\">1</span>");
        $response->assertSeeText("Notifications (0)");
        $response->assertDontSeeText($manga->getName());
    }
}