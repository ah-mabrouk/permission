<?php

namespace Mabrouk\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Mabrouk\Translatable\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory, Translatable;

    public $translatedAttributes = [
        'display_name',
        'description',
    ];

    protected $fillable = [
        'id',
        'permission_group_id',
        'name',
        'url',
    ];

    protected $with = [
        'subPermissions',
    ];

    ## Relations

    public function roles()
    {
        return $this->belongsToMany(Role::class)->using(PermissionRole::class, 'permission_id', 'role_id');
    }

    public function group()
    {
        return $this->belongsTo(PermissionGroup::class, 'permission_group_id');
    }

    public function permissionRoles()
    {
        return $this->hasMany(PermissionRole::class, 'permission_id');
    }

    public function subPermissions()
    {
        return $this->hasMany(SubPermission::class, 'permission_id');
    }

    ## Getters & Setters

    public function getIsSelectedAttribute()
    {
        if (\is_array(request()->permissionsIds)) {
            return \in_array($this->id, request()->permissionsIds);
        }
        return false;
    }

    public function getCustomDisplayNameAttribute()
    {
        return __('mabrouk/permission/permissions.custom_display_name.' . $this->name);
    }

    ## Query Scope Methods

    ## Other Methods
}
