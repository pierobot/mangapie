<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RolePermission extends Pivot
{
    // TODO: Find a way to remove this and not cause a disaster -_-
    protected $table = 'role_permissions';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
