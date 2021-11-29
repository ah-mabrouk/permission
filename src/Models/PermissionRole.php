<?php

namespace Mabrouk\RolePermissionGroup\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PermissionRole extends Pivot
{
    protected $table = 'permission_role';

    protected $fillable = [
        'id',
        'permission_id',
        'role_id',
    ];

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function subPermissions()
    {
        return $this->belongsToMany(SubPermission::class, 'permission_role_sub_permission', 'permission_role_id', 'sub_permission_id');
    }

    public function permissionRoleSubPermissions()
    {
        return $this->hasMany(PermissionRoleSubPermission::class, 'permission_role_id');
    }
}
