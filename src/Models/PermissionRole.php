<?php

namespace Mabrouk\Permission\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PermissionRole extends Pivot
{
    protected $table = 'permission_role';

    protected $fillable = [
        'id',
        'permission_id',
        'role_id',
    ];

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function subPermissions(): BelongsToMany
    {
        return $this->belongsToMany(SubPermission::class, 'permission_role_sub_permission', 'permission_role_id', 'sub_permission_id');
    }

    public function permissionRoleSubPermissions(): HasMany
    {
        return $this->hasMany(PermissionRoleSubPermission::class, 'permission_role_id');
    }

    ## Getters & Setters

    ## Query Scope Methods

    ## Other Methods
}
