<?php

namespace Mabrouk\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Mabrouk\Translatable\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubPermission extends Model
{
    use HasFactory, Translatable;

    public const ROUTE_METHOD_MAP = [
        'GET' => 'view',
        'POST' => 'create',
        'PUT' => 'edit',
        'DELETE' => 'delete',
    ];
    
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

    public function getCustomDisplayNameAttribute()
    {
        $routeMethod = request()->route()->methods()[0];

        return trans('mabrouk/permission/permissions.custom_sub_permission_display_name.' . self::ROUTE_METHOD_MAP[$routeMethod]);
    }    

    ## Query Scope Methods

    ## Other Methods
}
