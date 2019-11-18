<?php

namespace Tests\Unit;

use App\Comment;
use App\Library;
use App\User;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @covers \App\User
 * @covers \App\UserPermission
 */
class UserPermissionsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->runDatabaseMigrations();

        $this->seed([
            \UsersTableSeeder::class,
            \PermissionsTableSeeder::class,
            \RolesTableSeeder::class
        ]);
    }

    /**
     * @throws \Throwable
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Asserts that user permissions are granted and revoked from a class.
     */
    public function testClassPermissions()
    {
        $user = factory(User::class)->create();
        $user->grantPermission('create', Comment::class);

        $this->assertTrue($user->hasPermission('create', Comment::class));

        $user->revokePermission('create', Comment::class);

        $this->assertFalse($user->hasPermission('create', Comment::class));
    }

    /**
     * Asserts that user permissions are granted and revoked from an object.
     */
    public function testObjectPermissions()
    {
        $user = factory(User::class)->create();
        $library = factory(Library::class)->create();

        $user->grantPermission('view', $library);

        $this->assertTrue($user->hasPermission('view', $library));

        $user->revokePermission('view', $library);

        $this->assertFalse($user->hasPermission('view', $library));
    }
}
