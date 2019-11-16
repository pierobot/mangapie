<?php

use Illuminate\Database\Seeder;

use App\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        self::permissions(\App\Comment::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'view'
        ]);

        self::permissions(\App\Genre::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'view'
        ]);

        self::permissions(\App\Library::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'view'
        ]);

        self::permissions(\App\Manga::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'view'
        ]);

        self::permissions(\App\Permission::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'view'
        ]);

        self::permissions(\App\Person::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'view'
        ]);

        self::permissions(\App\Role::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'view'
        ]);

        self::permissions(\App\User::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'view'
        ]);

        self::permissions(\App\Vote::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'view'
        ]);
    }

    /**
     * @param string $modelClass
     * @param string[] $actions
     */
    public static function permissions(string $modelClass, $actions)
    {
        foreach ($actions as $action) {
            Permission::updateOrCreate(['action' => $action, 'model_type' => $modelClass]);
        }
    }
}
