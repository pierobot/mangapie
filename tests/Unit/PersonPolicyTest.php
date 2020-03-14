<?php

namespace Tests\Unit;

use App\Person;
use App\Policies\PersonPolicy;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @covers \App\User
 * @covers \App\Policies\UserPolicy
 */
class PersonPolicyTest extends TestCase
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
    private $banned = null;
    /** @var PersonPolicy */
    private $policy = null;
    /** @var Person */
    private $person = null;

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
        $this->banned = factory(User::class)->create();

        $this->admin->grantRole('Administrator');
        $this->moderator->grantRole('Moderator');
        $this->editor->grantRole('Editor');
        $this->member->grantRole('Member');
        $this->banned->grantRole('Banned');

        $this->policy = policy(Person::class);
        $this->person = factory(Person::class)->create();
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
        $this->assertTrue($this->policy->view($this->admin, $this->person));
        $this->assertTrue($this->policy->view($this->moderator, $this->person));
        $this->assertTrue($this->policy->view($this->editor, $this->person));
        $this->assertTrue($this->policy->view($this->member, $this->person));
        $this->assertFalse($this->policy->view($this->banned, $this->person));
    }

    public function testCreate()
    {
        $this->assertTrue($this->policy->create($this->admin));
        $this->assertFalse($this->policy->create($this->moderator));
        $this->assertTrue($this->policy->create($this->editor));
        $this->assertFalse($this->policy->create($this->member));
        $this->assertFalse($this->policy->create($this->banned));

        $this->editor->revokeRole('Editor');

        $this->assertFalse($this->policy->create($this->editor));
    }

    public function testDelete()
    {
        $this->assertTrue($this->policy->delete($this->admin, $this->person));
        $this->assertFalse($this->policy->delete($this->moderator, $this->person));
        $this->assertFalse($this->policy->delete($this->editor, $this->person));
        $this->assertFalse($this->policy->delete($this->member, $this->person));
        $this->assertFalse($this->policy->delete($this->banned, $this->person));
    }

    public function testForceDelete()
    {
        $this->assertTrue($this->policy->forceDelete($this->admin, $this->person));
        $this->assertFalse($this->policy->forceDelete($this->moderator, $this->person));
        $this->assertFalse($this->policy->forceDelete($this->editor, $this->person));
        $this->assertFalse($this->policy->forceDelete($this->member, $this->person));
        $this->assertFalse($this->policy->forceDelete($this->banned, $this->person));
    }

    public function testRestore()
    {
        $this->assertTrue($this->policy->restore($this->admin, $this->person));
        $this->assertFalse($this->policy->restore($this->moderator, $this->person));
        $this->assertFalse($this->policy->restore($this->editor, $this->person));
        $this->assertFalse($this->policy->restore($this->member, $this->person));
        $this->assertFalse($this->policy->restore($this->banned, $this->person));
    }

    public function testUpdate()
    {
        $this->assertTrue($this->policy->update($this->admin, $this->person));
        $this->assertFalse($this->policy->update($this->moderator, $this->person));
        $this->assertTrue($this->policy->update($this->editor, $this->person));
        $this->assertFalse($this->policy->update($this->member, $this->person));
        $this->assertFalse($this->policy->update($this->banned, $this->person));

        $this->editor->revokeRole('Editor');

        $this->assertFalse($this->policy->update($this->editor, $this->person));
    }
}
