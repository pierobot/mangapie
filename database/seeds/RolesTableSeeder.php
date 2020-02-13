<?php

use App\Comment;
use App\Genre;
use App\Library;
use App\LibraryPrivilege;
use App\Manga;
use App\Person;
use App\Role;
use App\Permission;
use App\User;
use App\Vote;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        self::role('Administrator')
            ->grantAllPermissions(Comment::class)
            ->grantAllPermissions(Genre::class)
            ->grantAllPermissions(Library::class)
            ->grantAllPermissions(Manga::class)
            ->grantAllPermissions(Permission::class)
            ->grantAllPermissions(Person::class)
            ->grantAllPermissions(Role::class)
            ->grantAllPermissions(User::class)
            ->grantAllPermissions(Vote::class);

        self::role('Moderator')
            ->grantPermission('create', Comment::class)
            ->grantPermission('delete', Comment::class)
            ->grantPermission('restore', Comment::class)
            ->grantPermission('update', Comment::class);

        self::role('Editor')
            ->grantPermission('create', Comment::class)
            ->grantPermission('create', Person::class)
            ->grantPermission('update', Person::class)
            ->grantPermission('update', Manga::class);

        self::role('Member')
            ->grantPermission('create', Comment::class);

        self::role('Banned');
    }

    /**
     * @param string $name
     * @return Role
     */
    private static function role(string $name)
    {
        return Role::updateOrCreate(['name' => $name]);
    }
}
