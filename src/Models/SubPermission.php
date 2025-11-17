<?php

namespace Mabrouk\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function permissionRoles(): BelongsToMany
    {
        return $this->belongsToMany(PermissionRole::class, 'sub_permission_id', 'permission_role_id');
    }

    public function subPermissionPermissionRoles(): HasMany
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
        return __('mabrouk/permission/permissions.custom_sub_permission_display_name.' . $this->display_name);
    }

    ## Query Scope Methods

    ## Other Methods
}
