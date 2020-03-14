<?php

namespace Tests\Unit;

use App\Genre;

use App\Policies\GenrePolicy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;

/**
 * @covers \App\User
 * @covers \App\Policies\GenrePolicy
 */
class GenrePolicyTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /** @var User */
    private $admin = null;
    /** @var User */
    private $moderator = null;
    /** @var User */
    private $editor = null;
    /** @var User */
    private $member = null;
    /** @var User */
    private $banned = null;
    /** @var GenrePolicy $genrePolicy */
    private $genrePolicy = null;
    /** @var Genre */
    private $genre = null;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->runDatabaseMigrations();

        $this->seed([
            \GenresTableSeeder::class,
            \PermissionsTableSeeder::class,
            \RolesTableSeeder::class
        ]);

        $this->admin = factory(User::class)->create();
        $this->moderator = factory(User::class)->create();
        $this->editor = factory(User::class)->create();
        $this->member = factory(User::class)->create();
        $this->banned = factory(User::class)->create();

        $this->admin->grantRole('Administrator');
        $this->moderator->grantRole('Moderator');
        $this->editor->grantRole('Editor');
        $this->member->grantRole('Member');
        $this->banned->grantRole('Banned');

        $this->genrePolicy = policy(Genre::class);
        $this->genre = Genre::firstOrFail();
    }

    /**
     * @throws \Throwable
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testView()
    {
        $this->assertTrue($this->genrePolicy->view($this->admin, $this->genre));
        $this->assertFalse($this->genrePolicy->view($this->moderator, $this->genre));
        $this->assertFalse($this->genrePolicy->view($this->editor, $this->genre));
        $this->assertFalse($this->genrePolicy->view($this->member, $this->genre));
        $this->assertFalse($this->genrePolicy->view($this->banned, $this->genre));
    }

    public function testCreate()
    {
        $this->assertTrue($this->genrePolicy->create($this->admin));
        $this->assertFalse($this->genrePolicy->create($this->moderator));
        $this->assertFalse($this->genrePolicy->create($this->editor));
        $this->assertFalse($this->genrePolicy->create($this->member));
        $this->assertFalse($this->genrePolicy->create($this->banned));
    }

    public function testDelete()
    {
        $this->assertTrue($this->genrePolicy->delete($this->admin, $this->genre));
        $this->assertFalse($this->genrePolicy->delete($this->moderator, $this->genre));
        $this->assertFalse($this->genrePolicy->delete($this->editor, $this->genre));
        $this->assertFalse($this->genrePolicy->delete($this->member, $this->genre));
        $this->assertFalse($this->genrePolicy->delete($this->banned, $this->genre));
    }

    public function testForceDelete()
    {
        $this->assertTrue($this->genrePolicy->forceDelete($this->admin, $this->genre));
        $this->assertFalse($this->genrePolicy->forceDelete($this->moderator, $this->genre));
        $this->assertFalse($this->genrePolicy->forceDelete($this->editor, $this->genre));
        $this->assertFalse($this->genrePolicy->forceDelete($this->member, $this->genre));
        $this->assertFalse($this->genrePolicy->forceDelete($this->banned, $this->genre));
    }

    public function testRestore()
    {
        $this->assertTrue($this->genrePolicy->restore($this->admin, $this->genre));
        $this->assertFalse($this->genrePolicy->restore($this->moderator, $this->genre));
        $this->assertFalse($this->genrePolicy->restore($this->editor, $this->genre));
        $this->assertFalse($this->genrePolicy->restore($this->member, $this->genre));
        $this->assertFalse($this->genrePolicy->restore($this->banned, $this->genre));
    }

    public function testUpdate()
    {
        $this->assertTrue($this->genrePolicy->update($this->admin, $this->genre));
        $this->assertFalse($this->genrePolicy->update($this->moderator, $this->genre));
        $this->assertFalse($this->genrePolicy->update($this->editor, $this->genre));
        $this->assertFalse($this->genrePolicy->update($this->member, $this->genre));
        $this->assertFalse($this->genrePolicy->update($this->banned, $this->genre));
    }
}
