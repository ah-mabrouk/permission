<?php

namespace Mabrouk\RolePermissionGroup\Models;

use Illuminate\Database\Eloquent\Model;
use Mabrouk\Translatable\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubPermission extends Model
{
    use HasFactory, Translatable;

    public $translatedAttributes = [
        'display_name',
    ];

    protected $fillable = [
        'permission_id',
        'name',
    ];

    ## Relations

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function permissionRoles()
    {
        return $this->belongsToMany(PermissionRole::class, 'sub_permission_id', 'permission_role_id');
    }

    public function subPermissionPermissionRoles()
    {
        return $this->hasMany(PermissionRoleSubPermission::class, 'sub_permission_id');
    }

    ## Getters & Setters

    public function getIsSelectedAttribute()
    {
        if (\is_array(request()->subPermissionsIds)) {
            return \in_array($this->id, request()->subPermissionsIds);
        }
        return false;
    }

    ## Query Scope Methods

    ## Other Methods
}
