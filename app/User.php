<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [
        'id', 'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Applies a local scope to get users with the Administrator role.
     *
     * @param $query
     * @return Builder
     */
    public static function scopeAdministrators(Builder $query)
    {
        return $query->whereHas('roles', function (Builder $query) {
            $query->where('name', 'Administrator');
        });
    }

    /**
     * Applies a local scope to get users with the Moderator role.
     *
     * @param $query
     * @return Builder
     */
    public static function scopeModerators(Builder $query)
    {
        return $query->whereHas('roles', function (Builder $query) {
            $query->where('name', 'Moderator');
        });
    }

    /**
     * Applies a local scope to get users with the Editor role.
     *
     * @param $query
     * @return Builder
     */
    public static function scopeEditors(Builder $query)
    {
        return $query->whereHas('roles', function (Builder $query) {
            $query->where('name', 'Editor');
        });
    }

    /**
     * Gets a Collection of the libraries a user can access.
     *
     * @return Collection
     */
    public function libraries()
    {
        return Library::all()->filter(function (Library $library) {
            return $this->can('view', $library);
        });
    }

    /**
     * Gets a Builder of the manga a user can access.
     *
     * @return Builder
     */
    public function manga()
    {
        $libraries = $this->libraries()->transform(function (Library $library) {
            return $library->id;
        });

        return Manga::whereIn('library_id', $libraries);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getLastSeen()
    {
        return $this->last_seen;
    }

    public function getJoined()
    {
        return $this->created_at;
    }

    public function getAbout()
    {
        return $this->about;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function favorites()
    {
        return $this->hasMany(\App\Favorite::class);
    }

    public function watchReferences()
    {
        return $this->hasMany(\App\WatchReference::class);
    }

    public function readerHistory()
    {
        return $this->hasMany(\App\ReaderHistory::class);
    }

    public function votes()
    {
        return $this->hasMany(\App\Vote::class);
    }

    public function archiveViews()
    {
        return $this->hasMany(\App\ArchiveView::class);
    }

    public function mangaViews()
    {
        return $this->hasMany(\App\MangaView::class);
    }

    public function comments()
    {
        return $this->hasMany(\App\Comment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function completed()
    {
        return $this->hasMany(\App\Completed::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dropped()
    {
        return $this->hasMany(\App\Dropped::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function onHold()
    {
        return $this->hasMany(\App\OnHold::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planned()
    {
        return $this->hasMany(\App\Planned::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reading()
    {
        return $this->hasMany(\App\Reading::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(\App\Role::class, 'user_roles')
            ->using(\App\UserRole::class);
    }

    /**
     * Gets a cached Collection of roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function cachedRoles()
    {
        return \Cache::remember("{$this->name}.roles", now()->addHour(), function () {
            return $this->roles()->get();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(\App\Permission::class, 'user_permissions')
            ->using(\App\UserPermission::class);
    }

    /**
     * Gets whether or not a user has permission to perform an action on a class or object through its role.
     *
     * @param string $action
     * @param string|object $classOrObject
     * @return bool
     */
    private function hasPermissionThroughRole(string $action, $classOrObject)
    {
        if ($this->hasRole('Administrator'))
            return true;

        // Model::class will return a string - so if the parameter is a string then it's not a specific model
        $isObject = ! is_string($classOrObject);

        $permissions = $this->roles()
            ->whereHas('permissions', function (\Illuminate\Database\Eloquent\Builder $permissionQuery) use ($action, $classOrObject, $isObject) {
                $permissionQuery = $permissionQuery
                    ->where('action', $action)
                    ->where('model_type', $isObject ? get_class($classOrObject) : $classOrObject );

                if ($isObject) {
                    $permissionQuery = $permissionQuery->where('model_id', $classOrObject->id);
                }

                return $permissionQuery;
            });

        return !! $permissions->count();
    }

    /**
     * Gets whether or not a user has permission to perform an action on a class or object through itself.
     *
     * @param string $action
     * @param string|object $classOrObject
     * @return bool
     */
    private function hasExplicitPermission(string $action, $classOrObject)
    {
        if ($this->hasRole('Administrator'))
            return true;

        // Model::class will return a string - so if the parameter is a string then it's not a specific model
        $isObject = ! is_string($classOrObject);

        $permissions = $this->permissions()->where('action', $action);
        if ($isObject) {
            $permissions = $permissions->where('model_type', get_class($classOrObject))
                ->where('model_id', $classOrObject->id);
        } else {
            $permissions = $permissions->where('model_type', $classOrObject);
        }

        return !! $permissions->count();
    }

    /**
     * Gets whether or not a user has permission to perform an action on a class or object.
     *
     * @param string $action
     * @param string|object $classOrObject
     * @return bool
     */
    public function hasPermission(string $action, $classOrObject)
    {
        return $this->hasPermissionThroughRole($action, $classOrObject) ||
            $this->hasExplicitPermission($action, $classOrObject);
    }

    /**
     * Grants permission to perform an action on a class or object.
     *
     * @param string $action
     * @param $classOrObject
     * @return bool
     */
    public function grantPermission(string $action, $classOrObject)
    {
        // if the user inherits the permission from the role, then do not add an entry to the user permissions
        $permissionExistsThroughRole = $this->hasPermissionThroughRole($action, $classOrObject);
        if ($permissionExistsThroughRole)
            return true;

        // Model::class will return a string - so if the parameter is a string then it's not a specific model
        $isObject = ! is_string($classOrObject);

        $permission = Permission::where('action', $action)
            ->where('model_type', $isObject ? get_class($classOrObject) : $classOrObject );

        if ($isObject) {
            $permission = $permission->where('model_id', $classOrObject->id);
        }

        if (! $permission->count()) {
            // create the permission if it does not exist
            $permission = Permission::create([
                'action' => $action,
                'model_type' => $isObject ? get_class($classOrObject) : $classOrObject,
                'model_id' => $isObject ? $classOrObject->id : null
            ]);
        } else {
            $permission = $permission->firstOrFail();
        }

        $this->permissions()->detach($permission);
        $this->permissions()->attach($permission);

        return true;
    }

    /**
     * Revokes permission to perform an action on a class or object.
     *
     * @param string $action
     * @param string|object $classOrObject
     * @return bool
     */
    public function revokePermission(string $action, $classOrObject)
    {
        // if the user inherits the permission from the role, then do not add an entry to the user permissions
        $permissionExistsThroughRole = $this->hasPermissionThroughRole($action, $classOrObject);
        if ($permissionExistsThroughRole)
            return true;

        // Model::class will return a string - so if the parameter is a string then it's not a specific model
        $isObject = ! is_string($classOrObject);

        $permission = Permission::where('action', $action)
            ->where('model_type', $isObject ? get_class($classOrObject) : $classOrObject )
            ->where('model_id', $isObject ? $classOrObject->id : null);

        // fail if the permission was not found
        if (! $permission->count()) {
            return false;
        }

        $permission = $permission->firstOrFail();

        $this->permissions()->detach($permission);

        return true;
    }

    /**
     * Grants a role to the user.
     *
     * @param Role|string $nameOrObject
     * @return bool
     */
    public function grantRole($nameOrObject)
    {
        // Model::class will return a string - so if the parameter is a string then it's not a specific model
        $isObject = ! is_string($nameOrObject);

        $role = $isObject ?
            $nameOrObject :
            Role::where('name', $nameOrObject)->firstOrFail();

        $this->roles()->attach($role);

        \Cache::forget("{$this->name}.roles");
        \Cache::remember("{$this->name}.roles", now()->addHour(), function () {
            return $this->roles()->get();
        });

        return true;
    }

    /**
     * Revokes a role from the user.
     *
     * @param string|Role $nameOrObject
     * @return bool
     */
    public function revokeRole($nameOrObject)
    {
        // Model::class will return a string - so if the parameter is a string then it's not a specific model
        $isObject = ! is_string($nameOrObject);

        $role = $isObject ?
            $nameOrObject :
            Role::where('name', $nameOrObject)->firstOrFail();

        $this->roles()->detach($role);

        \Cache::forget("{$this->name}.roles");
        \Cache::remember("{$this->name}.roles", now()->addHour(), function () {
            return $this->roles()->get();
        });

        return true;
    }

    /**
     * Determine if a user has a role.
     *
     * @param string $name
     * @return bool
     */
    public function hasRole(string $name) : bool
    {
        $cachedRoles = $this->cachedRoles();

        return $cachedRoles->where('name', $name)->count() > 0;
    }

    public function hasAnyRole(string ... $names)
    {
        $roles = $this->cachedRoles();

        foreach ($roles as $role) {
            if (in_array($role->name, $names))
                return true;
        }

        return false;
    }
}
