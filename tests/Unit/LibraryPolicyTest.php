<?php

namespace Tests\Unit;

use App\Library;
use App\Policies\LibraryPolicy;
use App\Role;
use App\User;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\User
 * @covers \App\Genre
 * @covers \App\Policies\LibraryPolicy
 */
class LibraryPolicyTest extends TestCase
{
    use DatabaseMigrations;

    /** @var User */
    private $admin = null;
    /** @var User */
    private $moderator = null;
    /** @var User */
    private $editor = null;
    /** @var User */
    private $member = null;
    /** @var User */
    private $otherMember = null;
    /** @var User */
    private $banned = null;
    /** @var LibraryPolicy */
    private $policy = null;
    /** @var Library */
    private $library = null;
    /** @var Library */
    private $otherLibrary = null;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->runDatabaseMigrations();

        $this->seed([
            \PermissionsTableSeeder::class,
            \RolesTableSeeder::class
        ]);

        $this->admin = factory(User::class)->create();
        $this->moderator = factory(User::class)->create();
        $this->editor = factory(User::class)->create();
        $this->member = factory(User::class)->create();
        $this->otherMember = factory(User::class)->create();
        $this->banned = factory(User::class)->create();

        /** @var Role $otherMemberRole */
        $otherMemberRole = Role::updateOrCreate([
            'name' => 'OtherMember'
        ]);

        /** @var Role $memberRole */
        $memberRole = Role::where('name', 'Member')->firstOrFail();

        $this->admin->grantRole('Administrator');
        $this->moderator->grantRole('Moderator');
        $this->editor->grantRole('Editor');
        $this->member->grantRole('Member');
        $this->otherMember->grantRole('OtherMember');
        $this->banned->grantRole('Banned');

        $this->policy = policy(Library::class);
        $this->library = factory(Library::class)->create();
        $this->otherLibrary = factory(Library::class)->create();

        // grant permission to the roles to the view their respective libraries
        $memberRole->grantPermission('view', $this->library);
        $otherMemberRole->grantPermission('view', $this->otherLibrary);
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
        // these tests are role based
        $this->assertTrue($this->policy->view($this->admin, $this->library));
        $this->assertTrue($this->policy->view($this->admin, $this->otherLibrary));

        $this->assertFalse($this->policy->view($this->moderator, $this->library));
        $this->assertFalse($this->policy->view($this->moderator, $this->otherLibrary));

        $this->assertFalse($this->policy->view($this->editor, $this->library));
        $this->assertFalse($this->policy->view($this->editor, $this->otherLibrary));

        $this->assertTrue($this->policy->view($this->member, $this->library));
        $this->assertFalse($this->policy->view($this->member, $this->otherLibrary));

        $this->assertFalse($this->policy->view($this->otherMember, $this->library));
        $this->assertTrue($this->policy->view($this->otherMember, $this->otherLibrary));

        $this->assertFalse($this->policy->view($this->banned, $this->library));
        $this->assertFalse($this->policy->view($this->banned, $this->otherLibrary));

        // these tests are explicit permission based
        $this->member->revokeRole('Member');
        $this->otherMember->revokeRole('OtherMember');

        $this->member->grantPermission('view', $this->library);
        $this->otherMember->grantPermission('view', $this->otherLibrary);

        $this->assertTrue($this->policy->view($this->member, $this->library));
        $this->assertFalse($this->policy->view($this->member, $this->otherLibrary));

        $this->assertFalse($this->policy->view($this->otherMember, $this->library));
        $this->assertTrue($this->policy->view($this->otherMember, $this->otherLibrary));
    }

    public function testCreate()
    {
        $this->assertTrue($this->policy->create($this->admin));
        $this->assertFalse($this->policy->create($this->moderator));
        $this->assertFalse($this->policy->create($this->editor));
        $this->assertFalse($this->policy->create($this->member));
        $this->assertFalse($this->policy->create($this->banned));
    }

    public function testDelete()
    {
        $this->assertTrue($this->policy->delete($this->admin, $this->library));
        $this->assertFalse($this->policy->delete($this->moderator, $this->library));
        $this->assertFalse($this->policy->delete($this->editor, $this->library));
        $this->assertFalse($this->policy->delete($this->member, $this->library));
        $this->assertFalse($this->policy->delete($this->banned, $this->library));
    }

    public function testForceDelete()
    {
        $this->assertTrue($this->policy->forceDelete($this->admin, $this->library));
        $this->assertFalse($this->policy->forceDelete($this->moderator, $this->library));
        $this->assertFalse($this->policy->forceDelete($this->editor, $this->library));
        $this->assertFalse($this->policy->forceDelete($this->member, $this->library));
        $this->assertFalse($this->policy->forceDelete($this->banned, $this->library));
    }

    public function testRestore()
    {
        $this->assertTrue($this->policy->restore($this->admin, $this->library));
        $this->assertFalse($this->policy->restore($this->moderator, $this->library));
        $this->assertFalse($this->policy->restore($this->editor, $this->library));
        $this->assertFalse($this->policy->restore($this->member, $this->library));
        $this->assertFalse($this->policy->restore($this->banned, $this->library));
    }

    public function testUpdate()
    {
        $this->assertTrue($this->policy->update($this->admin, $this->library));
        $this->assertFalse($this->policy->update($this->moderator, $this->library));
        $this->assertFalse($this->policy->update($this->editor, $this->library));
        $this->assertFalse($this->policy->update($this->member, $this->library));
        $this->assertFalse($this->policy->update($this->banned, $this->library));
    }
}
