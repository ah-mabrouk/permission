<?php

namespace Mabrouk\RolePermissionGroup\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PermissionRoleSubPermission extends Pivot
{
    public $table = 'permission_role_sub_permission';

    protected $fillable = [
        'permission_role_id',
        'sub_permission_id',
    ];

    ## Relations

    public function permissionRole()
    {
        return $this->belongsTo(PermissionRole::class, 'permission_role_id');
    }

    public function subPermission()
    {
        return $this->belongsTo(SubPermission::class, 'sub_permission_id');
    }

    ## Getters & Setters

    ## Query Scope Methods

    ## Other Methods
}
