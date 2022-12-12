<?php

namespace Mabrouk\Permission\Traits;

use Mabrouk\Permission\Models\Role;
use Mabrouk\Permission\Models\Permission;
use Mabrouk\Permission\Models\SubPermission;

Trait HasPermission
{
    /**
     * return the authenticated user depending on model class in
     * config('permissions.project_owner_model')
     *
     * you may change this value by overriding this static method
     * in the predefined model class in config('permissions.project_owner_model')
     */
    public static function authUser()
    {
        return auth('api')->user();
    }

    public function roles()
    {
        return $this->morphToMany(Role::class, 'roleable');
    }

    public function permissions()
    {
        return Permission::whereIn('permissions.id', $this->roles->map(function ($role) {
            return $role->permissionsIds;
        })->flatten()->toArray())->distinct();
    }

    public function subPermissions()
    {
        return SubPermission::whereIn('sub_permissions.id', $this->roles->map(function ($role) {
            return $role->subPermissionsIds;
        })->flatten()->toArray())->distinct();
    }

    public function takeRole(Role $role)
    {
        $roleIds = $this->roles->flatten()->pluck('id')->toArray();
        if ((bool) $role) {
            $roleIds[] = $role->id;
        }
        $roleIds = \array_unique($roleIds);
        $this->roles()->sync($roleIds);
        return $this->refresh();
    }

    public function leaveRole(Role $role)
    {
        if ((bool) $role) {
            $this->roles()->detach($role->id);
        }
        return $this->refresh();
    }

    public function canAccess($subPermissionName)
    {
        return \in_array($subPermissionName, $this->subPermissionsNames);
    }

    public function leaveAllRoles()
    {
        $this->roles()->sync([]);
        return $this->refresh();
    }

    public function getPermissionsAttribute()
    {
        $this->permissions()->get()->unique()->filter();
    }

    public function getSubPermissionsAttribute()
    {
        return $this->subPermissions()->get();
    }

    public function getSubPermissionsNamesAttribute()
    {
        return $this->subPermissions()->pluck('name')->flatten()->toArray();
    }

    public function scopeHasPermissions($query1, array $permissionsIds = [])
    {
        $permissionsIds = \array_filter($permissionsIds, function ($id) {
            return \is_int($id);
        });
        if ((bool) $permissionsIds) {
            return $query1->where(function ($query2) use ($permissionsIds) {
                $query2->whereHas('roles', function ($query3) use ($permissionsIds) {
                    $query3->whereHas('permissions', function ($query4) use ($permissionsIds) {
                        $query4->whereIn('permissions.id', $permissionsIds);
                    });
                });
            });
        }
        return $query1;
    }
}
