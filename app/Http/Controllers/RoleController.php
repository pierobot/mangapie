<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\PatchRoleRequest;
use App\Library;
use App\Permission;
use App\Role;
use App\User;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

final class RoleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    public function resourceAbilityMap()
    {
        return [
            'create' => 'create',
            'destroy' => 'forceDelete',
            'store' => 'create',
            'grant' => 'update',
            'revoke' => 'update'
        ];
    }

    public function index()
    {
        $roles = Role::orderBy('name', 'asc')
            ->with('permissions')
            ->get();

        $libraries = Library::orderBy('name', 'asc')
            ->get();

        $allActions = [
            'create',
            'delete',
            'forceDelete',
            'restore',
            'update',
        ];

        return view('admin.roles')
            ->with('roles', $roles)
            ->with('libraries', $libraries)
            ->with('allActions', $allActions);
    }

    /**
     * Route to create a role.
     *
     * @param CreateRoleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateRoleRequest $request)
    {
        $libraryIds = $request->get('libraries');

        /** @var Role $role */
        $role = Role::create([
            'name' => $request->get('name')
        ]);

        return back()->with('success', 'The role has been created.');
    }

    /**
     * Route to destroy a role.
     * @note The Administrator cannot be deleted.
     *
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Role $role)
    {
        if ($role->name === "Administrator") {
            return back()->withErrors('The Administrator role cannot be deleted.');
        }

        $role->forceDelete();

        return back()->with('success', 'The role has been deleted.');
    }

    /**
     * Route to update the permissions of a Role.
     * The permissions are overridden.
     *
     * @param Role $role
     * @param PatchRoleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update(Role $role, PatchRoleRequest $request)
    {
        if ($role->name === "Administrator") {
            return back()->withErrors('The Administrator role cannot be modified.');
        }

        $items = $request->get('actions');

        /** @var Collection $classPermissionsToGrant */
        $classPermissionsToGrant = collect();
        /** @var Collection $objectPermissionsToGrant */
        $objectPermissionsToGrant = collect();

        foreach ($items as $item) {
            $modelType = Arr::get($item, 'model_type');

            // Get a Collection of permissions that act on a class
            if (Arr::has($item, 'class')) {
                $actions = Arr::get($item, 'class.actions');

                if (! empty($actions)) {
                    // Get the ids of all the permissions that match the model type and actions
                    $classPermissionsToGrant = $classPermissionsToGrant->merge(Permission::where('model_type', $modelType)
                        ->whereIn('action', $actions)
                        ->select(['id'])
                        ->get()
                        ->transform(function (Permission $permission) {
                            return $permission->id;
                        }));
                }
            }

            // Get a Collection of permissions that act on an object
            if (Arr::has($item, 'object')) {
                $objectItems = Arr::get($item, 'object');

                if (! empty($objectItems)) {
                    foreach ($objectItems as $objectIndex => $objectItem) {
                        $modelId = Arr::get($item, "object.${objectIndex}.model_id");
                        $actions = Arr::get($item, "object.${objectIndex}.actions");

                        if (! empty($actions)) {
                            // Get the ids of all the permissions that match the model type, actions, and model id
                            $objectPermissionsToGrant = $objectPermissionsToGrant->merge(Permission::where('model_type', $modelType)
                                ->where('model_id', $modelId)
                                ->whereIn('action', $actions)
                                ->select(['id'])
                                ->get()
                                ->transform(function (Permission $permission) {
                                    return $permission->id;
                                }));
                        }
                    }
                }
            }
        }

        $permissionsToGrant = collect()->merge($classPermissionsToGrant)
            ->merge($objectPermissionsToGrant);

        \DB::transaction(function () use ($role, $permissionsToGrant) {
            $role->permissions()->sync($permissionsToGrant);
        });

        return back()->with('success', 'The role has been updated.');
    }

    /**
     * Route to grant a role to a user.
     *
     * @param User $user
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function grant(User $user, Role $role)
    {
        $user->grantRole($role);

        return back(303)->with('success', $role->name . ' has been granted to ' . $user->name . '.');
    }

    /**
     * Route to revoke a role from a user.
     *
     * @param User $user
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revoke(User $user, Role $role)
    {
        $user->revokeRole($role);

        return back(303)->with('success', $role->name . ' has been revoked from ' . $user->name . '.');
    }
}
