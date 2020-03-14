<?php

namespace Tests\Unit;

use App\Comment;
use App\Manga;
use App\Role;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @covers \App\Role
 * @covers \App\RolePermission
 *
 */
class RolePermissionsTest extends TestCase
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
     * Asserts that role granting and revoking works.
     *
     * @param string ...$roles
     *
     * @testWith ["Administrator", "Member"]
     */
    public function testGrantRevokeRole(string ... $roles)
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        foreach ($roles as $role) {
            $user->grantRole($role);

            $this->assertTrue($user->hasRole($role));
        }

        $this->assertTrue($user->hasAnyRole(... $roles));

        foreach ($roles as $role) {
            $user->revokeRole($role);

            $this->assertFalse($user->hasRole($role));
        }
    }

    /**
     * Asserts that role permissions are granted and revoked from a class.
     */
    public function testClassPermissions()
    {
        /** @var Role $memberRole */
        $memberRole = Role::where('name', 'Member')->firstOrFail();
        /** @var Role $editorRole */
        $editorRole = Role::where('name', 'Editor')->firstOrFail();

        $this->assertTrue($memberRole->hasPermission('create', Comment::class));
        $this->assertTrue($editorRole->hasPermission('update', Manga::class));

        $memberRole->revokePermission('create', Comment::class);
        $editorRole->revokePermission('update', Manga::class);

        $this->assertFalse($memberRole->hasPermission('create', Comment::class));
        $this->assertFalse($editorRole->hasPermission('update', Manga::class));
    }

    /**
     * Asserts that role permissions are granted and revoked from an object.
     */
    public function testObjectPermissions()
    {
        /** @var Role $memberRole */
        $memberRole = Role::where('name', 'Member')->firstOrFail();
        $comment = factory(Comment::class)->create();

        $memberRole->grantPermission('update', $comment);
        $this->assertTrue($memberRole->hasPermission('update', $comment));

        $memberRole->revokePermission('update', $comment);
        $this->assertFalse($memberRole->hasPermission('update', $comment));
    }
}
