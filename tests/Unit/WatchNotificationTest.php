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
 *
 * @covers \App\Http\ViewComposers\NotificationsComposer
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

        $response = $this->actingAs($user)
                         ->withSession(['foo' => 'bar'])
                         ->get(\URL::action('NotificationController@index'));

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

        $response = $this->actingAs($user)
                         ->withSession(['foo' => 'bar'])
                         ->get(\URL::action('NotificationController@index'));

        $response->assertViewIs('notifications.index');
        $response->assertDontSee("<span class=\"badge\" id=\"notification-count\">1</span>");
        $response->assertSeeText("Notifications (0)");
        $response->assertDontSeeText($manga->getName());
    }

    public function testDismissSelectedNotifications()
    {
        $watchReference = factory(WatchReference::class)->create();
        $user = $watchReference->user;
        $manga = $watchReference->manga;

        factory(Archive::class, 5)->create([
            'manga_id' => $manga->getId()
        ]);

        // simulate checking the first 3 of 5 notifications
        $notifications = $user->watchNotifications->take(3);

        $ids = [];
        foreach ($notifications as $notification) {
            array_push($ids, $notification->getId());
        }

        $response = $this->actingAs($user)
                         ->withSession(['foo' => 'bar'])
                         ->followingRedirects()
                         ->post(\URL::action('NotificationController@dismiss'), [
                             'action' => 'dismiss.selected',
                             'ids' => $ids
                         ]);

        $response->assertViewIs('notifications.index');

        $this->assertDatabaseHas('watch_notifications', [
            'user_id' => $user->getId(),
            'manga_id' => $manga->getId(),
        ]);

        $response->assertSee("<span class=\"badge\" id=\"notification-count\">2</span>");
        $response->assertSeeText("Notifications (2)");
    }

    public function testDismissAllNotifications()
    {
        $watchReference = factory(WatchReference::class)->create();
        $user = $watchReference->user;
        $manga = $watchReference->manga;

        factory(Archive::class, 5)->create([
            'manga_id' => $manga->getId()
        ]);

        $notifications = $user->watchNotifications;

        $ids = [];
        foreach ($notifications as $notification) {
            array_push($ids, $notification->getId());
        }

        $response = $this->actingAs($user)
                         ->withSession(['foo' => 'bar'])
                         ->followingRedirects()
                         ->post(\URL::action('NotificationController@dismiss'), [
                             'action' => 'dismiss.all',
                             'ids' => $ids
                         ]);

        $response->assertViewIs('notifications.index');

        $this->assertDatabaseMissing('watch_notifications', [
            'user_id' => $user->getId(),
            'manga_id' => $manga->getId(),
        ]);

        $response->assertSeeText("Notifications (0)");
    }
}
