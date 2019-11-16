<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPermissionsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

//        $this->runDatabaseMigrations();
//
//        $this->seed([
//            \UsersTableSeeder::class,
//            \PermissionsTableSeeder::class,
//            \RolesTableSeeder::class
//        ]);
    }

    /**
     * @throws \Throwable
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
