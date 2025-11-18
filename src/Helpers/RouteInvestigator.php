<?php

namespace Mabrouk\Permission\Helpers;

use Illuminate\Routing\Route as Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Mabrouk\Permission\Models\Permission;

class RouteInvestigator
{
    const ROUTE_METHOD_MAP = [
        'GET' => 'view',
        'POST' => 'create',
        'PUT' => 'edit',
        'DELETE' => 'delete',
    ];

    public $baseUrls;
    public $excludedRoutes;
    public $existingPermissions;
    public $permissionableRoutes;

    public function __construct()
    {
        $this->baseUrls = config('permissions.base_urls') ?? [];
        $this->excludedRoutes = config('permissions.excluded_routes') ?? [];
        request()->routeInvestigator = $this;
        $this->existingPermissions = Permission::all();
        $this->permissionableRoutes = $this->permissionableRoutes();
    }

    /**
     *
     * get all permissions from routes which need permissions
     */
    public function createPermissions() : collection
    {
        $permissions = $this->permissionableRoutes->map(function ($route) {
            $newPermissionObject = new Permission([
                'permission_group_id' => config('permissions.base_permission_group_id'),
                'url' => $this->sanitizeBaseUrl($route->uri()),
            ]);
            $newPermissionObject->name = ! Str::contains($newPermissionObject->url, '{') ? $newPermissionObject->url : $this->sanitizeRouteModelIdentifier($newPermissionObject->url);

            if (config('app.env') == 'local') {
                $fallbackHasName = Lang::hasForLocale('mabrouk/permission/permissions.custom_display_name.' . $newPermissionObject->name, config('translatable.fallback_locale'));
                $secondLocale = config('translatable.fallback_locale') == 'en' ? 'ar' : 'en';
                $secondLocaleHasName = Lang::hasForLocale('mabrouk/permission/permissions.custom_display_name.' . $newPermissionObject->name, $secondLocale);

                if (!$fallbackHasName || !$secondLocaleHasName) {
                    $msg = "Missing translation key [permissions.{$newPermissionObject->name}], Please check all lang files";
                    throw new \RuntimeException($msg);
                }
            }

            return $newPermissionObject;
        })
        ->unique('name')
        ->filter(function ($permission) {
            return $permission->name != null && ! \in_array($permission->name, $this->existingPermissions->pluck('name')->flatten()->toArray());
        });

        return $this->saveBulkPermissions($permissions);
    }

    private function saveBulkPermissions(Collection $permissionCollection) : Collection
    {
        Permission::insert($permissionCollection->toArray());
        $this->existingPermissions = Permission::all();
        return $this->existingPermissions->map(function ($permission) {
            $this->createSubPermissionsOf($permission);
            $permission->with('subPermissions');
            return $permission->refresh();
        });
    }

    /**
     *
     * get all subPermissions of a permission from routes which need subPermissions
     */
    public function createSubPermissionsOf(Permission $permission) : collection
    {
        return $this->permissionableRoutes->filter(function ($route) use ($permission) {
            return $this->permissionIsRelatedToRoute($permission, $route) && ! \in_array($this->subPermissionNameFromRoute($permission, $route), $this->subPermissionsNamesOf($permission));
        })->map(function ($route) use ($permission) {
            $subPermissionName = $this->subPermissionNameFromRoute($permission, $route);

            $newSubPermissionAttributes = [
                'permission_id' => $permission->id,
                'name' => $subPermissionName,
                'display_name' => $this->getSubPermissionDisplayNameForRoute($route, $subPermissionName),
            ];
            return $newSubPermissionAttributes;
        })->unique('name');
    }

    private function getSubPermissionDisplayNameForRoute(Router $route, string $subPermissionName): string
    {
        $customDisplayNames = config('permissions.custom_sub_permissions_display_name');

        var_dump($route->getName(), \array_key_exists($route->getName(), $customDisplayNames));

        if (\array_key_exists($route->getName(), $customDisplayNames)) {
            return $customDisplayNames[$route->getName()];
        }

        return \explode('_', $subPermissionName)[1];
    }

    private function permissionIsRelatedToRoute(Permission $permission, Router $route)
    {
        return $this->sanitizeBaseUrl($route->uri()) == $permission->url || $this->sanitizeBaseUrl($route->uri()) == $permission->url . '/' . $this->getLastRouteSegmentIdentifier($route->uri());
    }

    private function subPermissionNameFromRoute(Permission $permission, Router $route) : string
    {
        $url = $this->sanitizeRouteModelIdentifier($permission->url);
        return "{$url}_" . self::ROUTE_METHOD_MAP[$route->methods()[0]];
    }

    public function subPermissionsNamesOf(Permission $permission) : array
    {
        return $permission->subPermissions->map(function ($subPermission) {
            return $subPermission->name;
        })->flatten()->toArray();
    }

    /**
     *
     * check just for in app routes
     */
    public function permissionableRoutes()
    {
        return collect(Route::getRoutes()->get())->filter(function ($route) {
            return $this->routeIsPermissionable($route);
        });
    }

    /**
     *
     * check routes against permissions config ['baseUrls', 'excluded routes']
     */
    public function routeIsPermissionable(Router $route) : bool
    {
        if (\str_starts_with($route->uri(), '_') || in_array($route->getName(), $this->excludedRoutes))
            return false;

        for ($i = 0; $i < \count($this->baseUrls) ; $i++) {
            if (Str::contains($route->uri(), $this->baseUrls[$i])) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * exclude any of permissions config 'baseUrls' from route
     */
    public function sanitizeBaseUrl(string $url) : string
    {
        for ($i = 0; $i < \count($this->baseUrls); $i++) {
            if (Str::contains($url, $this->baseUrls[$i])) {
                $urlParts = \explode($this->baseUrls[$i] . '/', $url);
                return \count($urlParts) > 1 ? $urlParts[1] : $urlParts[0];
            }
        }

        return $url;
    }

    public function sanitizeRouteModelIdentifier(string $url) : string
    {
        $segments = collect(\explode('/', $url));
        $segments = $segments->filter(function ($segment) {
            return ! Str::contains($segment, '{');
        })->flatten()->toArray();

        return \implode('/', $segments);
    }

    public function getLastRouteSegmentIdentifier(string $url) : string
    {
        $lastSegment = collect(\explode('/', $url))->last();

        return Str::contains($lastSegment, '{') ? $lastSegment : '';
    }
}
