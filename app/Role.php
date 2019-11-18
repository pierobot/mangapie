<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->using(UserRole::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->using(RolePermission::class);
    }

    /**
     * Gets a Permission model from the given action and class/object.
     *
     * @param string $action
     * @param string|object $classOrObject
     * @return Permission
     */
    private function permissionFromAction(string $action, $classOrObject)
    {
        // Model::class will return a string - so if the parameter is a string then it's not a specific model
        $isObject = ! is_string($classOrObject);

        $permissions = Permission::where('action', $action);
        if ($isObject) {
            $permissions = $permissions->where('model_type', get_class($classOrObject))
                ->where('model_id', $classOrObject->id);
        } else {
            $permissions = $permissions->where('model_type', $classOrObject);
        }

        // if the permission does not exist, then create it
        // the situation where this will occur is when granting permission to view a specific object
        $permission = $permissions->first();
        if (empty($permission)) {
            if (! $isObject) {
                $permission = Permission::updateOrCreate([
                    'action' => $action,
                    'model_type' => $classOrObject
                ]);
            } else {
                $permission = Permission::updateOrCreate([
                    'action' => $action,
                    'model_type' => get_class($classOrObject),
                    'model_id' => $classOrObject->id
                ]);
            }

        }

        return $permission;
    }

    /**
     * Gets all Permission models where model_type matches $className.
     *
     * @param string $className
     * @return Collection
     */
    private function permissionsForClass(string $className)
    {
        return Permission::where('model_type', $className)->get();
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
        $permission = $this->permissionFromAction($action, $classOrObject);

        return RolePermission::where('role_id', $this->id)
            ->where('permission_id', $permission->id)
            ->count() > 0;
    }

    /**
     * Grants permission to perform an action on a class or object.
     *
     * @param string $className
     * @return $this
     */
    public function grantAllPermissions(string $className)
    {
        $permissions = $this->permissionsForClass($className);

        // attach will throw a QueryException if it already exists
        $this->permissions()->detach($permissions);
        $this->permissions()->attach($permissions);

        return $this;
    }

    /**
     * Grants permission to perform an action on a class or object.
     *
     * @param string $action
     * @param string|object $classOrObject
     * @return $this
     */
    public function grantPermission(string $action, $classOrObject)
    {
        $permission = $this->permissionFromAction($action, $classOrObject);

        // attach will throw a QueryException if it already exists
        $this->permissions()->detach($permission);
        $this->permissions()->attach($permission);

        return $this;
    }

    /**
     * Revokes permission to perform an action on a class or object.
     *
     * @param string $action
     * @param string|object $classOrObject
     * @return $this
     */
    public function revokePermission(string $action, $classOrObject)
    {
        $permission = $this->permissionFromAction($action, $classOrObject);

        $this->permissions()->detach($permission);

        return $this;
    }
}
