<?php

namespace Mabrouk\Permission\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Mabrouk\Filterable\Traits\Filterable;
use Mabrouk\Translatable\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, Translatable, Filterable;

    public $translatedAttributes = [
        'name',
        'description',
    ];

    protected $fillable = [
        'id',

        'deleted_at',
    ];

    ## Relations

    public function relatedModel($query, $relatedModelClass, ...$params): MorphToMany
    {
        return $this->morphedByMany($relatedModelClass, 'roleable');
    }

    public function roleableRecords(): HasMany
    {
        return $this->hasMany(Roleable::class, 'role_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)->using(PermissionRole::class, 'role_id', 'permission_id');
    }

    public function rolePermissions(): HasMany
    {
        return $this->hasMany(PermissionRole::class, 'role_id');
    }

    ## Getters & Setters

    public function getPermissionsIdsAttribute()
    {
        return $this->permissions()->pluck('permissions.id')->flatten()->toArray();
    }

    public function getSubPermissionsIdsAttribute()
    {
        return $this->rolePermissions()->with('subPermissions:id')->get()->flatMap(function ($rolePermission) {
            return $rolePermission->subPermissions->pluck('id');
        })->toArray();
    }

    ## Query Scope Methods

    ## Other Methods

    public function __call($methodName, $params)
    {
        return \array_key_exists($methodName, config('permissions.roleable_models')) ? $this->relatedModel($methodName, config("permissions.roleable_models.{$methodName}"), $params) : Parent::__call($methodName, $params);
    }

    public function syncPermissions(array $permissions) : self
    {
        if (\count($permissions) > 0) {
            $permissions = collect($permissions);
            $permissionsIds = $permissions->map(function ($subPermissionsIds, $permissionId) {
                return $permissionId;
            })->flatten()->toArray();
            $this->permissions()->sync($permissionsIds);
            $this->refresh()->syncSubPermissions($permissions);
        }

        return $this->refresh();
    }

    private function syncSubPermissions(Collection $permissions): void
    {
        $this->rolePermissions->each(function ($rolePermission) use ($permissions) {
            $permissions->each(function ($subPermissionsIds, $permissionId) use ($rolePermission) {
                if ($rolePermission->permission_id == $permissionId) {
                    $rolePermission->subPermissions()->sync($subPermissionsIds);
                }
            });
        });

        $this->refreshRoleablesSubPermissionsCache();
    }

    private function refreshRoleablesSubPermissionsCache(): void
    {
        $this->roleableRecords()
            ->select(['id', 'roleable_type','roleable_id'])
            ->chunkById(500, function ($chunk) {
                $this->refreshRoleablesSubPermissionsCacheFrom($chunk);
            });
    }

    private function refreshRoleablesSubPermissionsCacheFrom($roleables): void
    {
        collect($roleables)->each(function ($roleable) {
            $model = $roleable->roleable_type::find($roleable->roleable_id);
            $model?->cacheSubPermissionNames(force: true);
        });
    }

    public function remove(): self
    {
        $response = [];
        $response['message'] = __('mabrouk/permission/roles.destroy');
        $response['response_code'] = 200;
        switch (true) {
            case $this->id == 0:
                $response['message'] = __('mabrouk/permission/roles.cant_destroy_super_admin_role');
                $response['response_code'] = 409;
                break;
            case (bool) $this->users()->count() :
                $response['message'] = __('mabrouk/permission/roles.cant_destroy');
                $response['response_code'] = 409;
            break;
        }
        if ($response['response_code'] == 200) {
            $this->permissions()->sync([]);
            $this->deleteTranslations();
            $this->delete();
        }
        $this->response = $response;

        return $this;
    }
}
