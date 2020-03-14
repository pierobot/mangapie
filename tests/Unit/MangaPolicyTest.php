<?php

namespace Tests\Unit;

use App\Library;
use App\Manga;
use App\Policies\MangaPolicy;
use App\Role;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\User
 * @covers \App\Manga
 * @covers \App\Policies\MangaPolicy
 */
class MangaPolicyTest extends TestCase
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
    /** @var MangaPolicy */
    private $policy = null;
    /** @var Library */
    private $library = null;
    /** @var Library */
    private $otherLibrary = null;
    /** @var Manga */
    private $manga = null;
    /** @var Manga */
    private $otherManga = null;

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

        $this->policy = policy(Manga::class);
        $this->library = factory(Library::class)->create();
        $this->otherLibrary = factory(Library::class)->create();

        $this->manga = factory(Manga::class)->create([
            'library_id' => $this->library->id
        ]);
        $this->otherManga = factory(Manga::class)->create([
            'library_id' => $this->otherLibrary->id
        ]);

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
        $this->assertTrue($this->policy->view($this->admin, $this->manga));
        $this->assertTrue($this->policy->view($this->admin, $this->otherManga));

        $this->assertFalse($this->policy->view($this->moderator, $this->manga));
        $this->assertFalse($this->policy->view($this->moderator, $this->otherManga));

        $this->assertFalse($this->policy->view($this->editor, $this->manga));
        $this->assertFalse($this->policy->view($this->editor, $this->otherManga));

        $this->assertTrue($this->policy->view($this->member, $this->manga));
        $this->assertFalse($this->policy->view($this->member, $this->otherManga));

        $this->assertFalse($this->policy->view($this->otherMember, $this->manga));
        $this->assertTrue($this->policy->view($this->otherMember, $this->otherManga));

        $this->assertFalse($this->policy->view($this->banned, $this->manga));
        $this->assertFalse($this->policy->view($this->banned, $this->otherManga));

        // these tests are explicit permission based
        $this->member->revokeRole('Member');
        $this->otherMember->revokeRole('OtherMember');

        $this->member->grantPermission('view', $this->manga);
        $this->otherMember->grantPermission('view', $this->otherManga);

        $this->assertTrue($this->policy->view($this->member, $this->manga));
        $this->assertFalse($this->policy->view($this->member, $this->otherManga));

        $this->assertFalse($this->policy->view($this->otherMember, $this->manga));
        $this->assertTrue($this->policy->view($this->otherMember, $this->otherManga));
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
        $this->assertTrue($this->policy->delete($this->admin, $this->manga));
        $this->assertFalse($this->policy->delete($this->moderator, $this->manga));
        $this->assertFalse($this->policy->delete($this->editor, $this->manga));
        $this->assertFalse($this->policy->delete($this->member, $this->manga));
        $this->assertFalse($this->policy->delete($this->banned, $this->manga));
    }

    public function testForceDelete()
    {
        $this->assertTrue($this->policy->forceDelete($this->admin, $this->manga));
        $this->assertFalse($this->policy->forceDelete($this->moderator, $this->manga));
        $this->assertFalse($this->policy->forceDelete($this->editor, $this->manga));
        $this->assertFalse($this->policy->forceDelete($this->member, $this->manga));
        $this->assertFalse($this->policy->forceDelete($this->banned, $this->manga));
    }

    public function testRestore()
    {
        $this->assertTrue($this->policy->restore($this->admin, $this->manga));
        $this->assertFalse($this->policy->restore($this->moderator, $this->manga));
        $this->assertFalse($this->policy->restore($this->editor, $this->manga));
        $this->assertFalse($this->policy->restore($this->member, $this->manga));
        $this->assertFalse($this->policy->restore($this->banned, $this->manga));
    }

    public function testUpdate()
    {
        $this->assertTrue($this->policy->update($this->admin, $this->manga));
        $this->assertFalse($this->policy->update($this->moderator, $this->manga));
        $this->assertTrue($this->policy->update($this->editor, $this->manga));
        $this->assertFalse($this->policy->update($this->member, $this->manga));
        $this->assertFalse($this->policy->update($this->banned, $this->manga));
    }
}
