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
            'create', 'delete', 'forceDelete', 'restore', 'update', 'viewAny'
        ]);

        self::permissions(\App\Genre::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'viewAny'
        ]);

        self::permissions(\App\Library::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'viewAny'
        ]);

        self::permissions(\App\Manga::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'viewAny'
        ]);

        self::permissions(\App\Permission::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'viewAny'
        ]);

        self::permissions(\App\Person::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'viewAny'
        ]);

        self::permissions(\App\Role::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'viewAny'
        ]);

        self::permissions(\App\User::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'viewAny'
        ]);

        self::permissions(\App\Vote::class, [
            'create', 'delete', 'forceDelete', 'restore', 'update', 'viewAny'
        ]);

        // Create a view permission for all the present libraries
        $libraries = \App\Library::all();
        /** @var \App\Library $library */
        foreach ($libraries as $library) {
            self::permissions(\App\Library::class, ['view'], $library->id);
        }
    }

    /**
     * @param string $modelClass
     * @param string[] $actions
     * @param int $modelId
     */
    public static function permissions(string $modelClass, array $actions, int $modelId = null)
    {
        foreach ($actions as $action) {
            Permission::updateOrCreate([
                'action' => $action,
                'model_type' => $modelClass,
                'model_id' => $modelId
            ]);
        }
    }
}
