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
 * @covers \App\Http\Requests\Watch\WatchCreateRequest
 * @covers \App\Http\Requests\Watch\WatchDeleteRequest
 * @covers \App\Http\ViewComposers\NotificationsComposer
 */
class WatchNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
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
            ->from(\URL::action('MangaController@index', [$manga]))
            ->post(\URL::action('WatchController@create'), [
                'manga_id' => $manga->id
            ]);

        $this->assertDatabaseHas('watch_references', [
            'user_id' => $user->id,
            'manga_id' => $manga->id,
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
        $watchReference = $user->watchReferences()->create([
            'manga_id' => $manga->id
        ]);

        $response = $this->actingAs($user)
            ->withSession(['foo' => 'bar'])
            ->followingRedirects()
            ->from(\URL::action('MangaController@index', [$manga]))
            ->delete(\URL::action('WatchController@delete'), [
                'watch_reference_id' => $watchReference->id
            ]);

        $this->assertDatabaseMissing('watch_references', [
            'user_id' => $user->id,
            'manga_id' => $manga->id,
        ]);
        $response->assertSee('You are no longer watching this manga.');
    }

    public function testUserReceivesNotificationIfWatching()
    {
        $watchReference = factory(WatchReference::class)->create();
        $user = $watchReference->user;
        $manga = $watchReference->manga;

        factory(Archive::class)->create([
            'manga_id' => $manga->id
        ]);

        $response = $this->actingAs($user)
            ->withSession(['foo' => 'bar'])
            ->get(\URL::action('NotificationController@index'));

        $response->assertViewIs('notifications.index');
        $response->assertSeeText("Notifications (1)");
        $response->assertSeeText($manga->name);
    }

    public function testUserDoesNotReceiveNotificationIfUnwatched()
    {
        $watchReference = factory(WatchReference::class)->create();
        $user = $watchReference->user;
        $manga = $watchReference->manga;

        $watchReference->forceDelete();

        factory(Archive::class)->create([
            'manga_id' => $manga->id
        ]);

        $response = $this->actingAs($user)
            ->withSession(['foo' => 'bar'])
            ->get(\URL::action('NotificationController@index'));

        $response->assertViewIs('notifications.index');
        $response->assertSeeText("Notifications (0)");
        $response->assertDontSeeText($manga->name);
    }

    public function testDismissSelectedNotifications()
    {
        $watchReference = factory(WatchReference::class)->create();
        $user = $watchReference->user;
        $manga = $watchReference->manga;

        factory(Archive::class, 5)->create([
            'manga_id' => $manga->id
        ]);

        // simulate checking the first 3 of 5 notifications
        $notifications = $user->watchNotifications->take(3);

        $ids = [];
        foreach ($notifications as $notification) {
            $ids[] = $notification->id;
        }

        $response = $this->actingAs($user)
            ->withSession(['foo' => 'bar'])
            ->followingRedirects()
            ->from(\URL::action('NotificationController@index'))
            ->delete(\URL::action('NotificationController@delete'), [
                'ids' => $ids,
                'action' => 'dismiss.selected'
            ]);

        $response->assertViewIs('notifications.index');

        $this->assertDatabaseHas('watch_notifications', [
            'user_id' => $user->id,
            'manga_id' => $manga->id,
        ]);

        $response->assertSeeText("Notifications (2)");
    }

    public function testDismissAllNotifications()
    {
        $watchReference = factory(WatchReference::class)->create();
        $user = $watchReference->user;
        $manga = $watchReference->manga;

        factory(Archive::class, 5)->create([
            'manga_id' => $manga->id
        ]);

        $notifications = $user->watchNotifications;

        $ids = [];
        foreach ($notifications as $notification) {
            $ids[] = $notification->id;
        }

        $response = $this->actingAs($user)
            ->withSession(['foo' => 'bar'])
            ->followingRedirects()
            ->from(\URL::action('NotificationController@index'))
            ->delete(\URL::action('NotificationController@delete'), [
                'ids' => $ids,
                'action' => 'dismiss.all'
            ]);

        $response->assertViewIs('notifications.index');

        $this->assertDatabaseMissing('watch_notifications', [
            'user_id' => $user->id,
            'manga_id' => $manga->id,
        ]);

        $response->assertSeeText("Notifications (0)");
    }
}
