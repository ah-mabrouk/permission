<?php

namespace Mabrouk\Permission\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Cache;
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

    ## Relations

    public function roles(): MorphToMany
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

    ## Getters & Setters

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
        return $this->cacheSubPermissionNames();
    }

    ## Query Scope Methods

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

    ## Other Methods

    public function takeRole(Role $role): self
    {
        $roleIds = $this->roles->flatten()->pluck('id')->toArray();
        if ((bool) $role) {
            $roleIds[] = $role->id;
        }
        $roleIds = \array_unique($roleIds);
        $this->roles()->sync($roleIds);

        $this->refresh();
        $this->cacheSubPermissionNames(force: true);

        return $this;
    }

    public function leaveRole(Role $role): self
    {
        if ((bool) $role) {
            $this->roles()->detach($role->id);
        }

        $this->refresh();
        $this->cacheSubPermissionNames(force: true);

        return $this;
    }

    public function canAccess($subPermissionName): bool
    {
        return \in_array($subPermissionName, $this->subPermissionsNames);
    }

    public function leaveAllRoles(): self
    {
        $this->roles()->sync([]);

        $this->refresh();
        $this->cacheSubPermissionNames(force: true);

        return $this;
    }

    /**
     * Cache helpers
     */
    protected function getSubPermissionsCacheKey(): string
    {
        $cacheKey = "perm:user:{$this->getKey()}";
        if (request()->company) {
            $cacheKey .= ":company:".request()->company?->cachePrefix();
        }

        return $cacheKey;
    }

    public function cacheSubPermissionNames(bool $force = false): array
    {
        if ($force) {
            $this->invalidateCachedSubPermissionNames();
        }

        $ttl = (int) config('permissions.cache_ttl_minutes', 120);

        return Cache::remember($this->getSubPermissionsCacheKey(), now()->addMinutes($ttl), function () {
            return $this->subPermissions()->pluck('name')->flatten()->toArray();
        });
    }

    public function hasCachedSubPermissionNames(): bool
    {
        return Cache::has($this->getSubPermissionsCacheKey());
    }

    public function invalidateCachedSubPermissionNames(): void
    {
        Cache::forget($this->getSubPermissionsCacheKey());
    }
}
