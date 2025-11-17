<?php

namespace Mabrouk\Permission\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class PermissionOfficerMiddleware
{
    const ROUTE_METHOD_MAP = [
        'GET' => 'view',
        'POST' => 'create',
        'PUT' => 'edit',
        'DELETE' => 'delete',
    ];

    public array $baseUrls;
    public array $excludedRoutes;
    public array $subPermissionsNames;

    public function __construct()
    {
    }

    private function authUser()
    {
        return config('permissions.project_owner_model')::authUser();
    }

    /**
     * Handle an incoming request.
     *
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();
        $uri = $route?->uri() ?? '';
        $this->baseUrls = config('permissions.base_urls') ?? [];
        $this->excludedRoutes = config('permissions.excluded_routes') ?? [];
        $this->subPermissionsNames = $this->authUser()?->subPermissionsNames ?? [];
        $routeSubPermissionName = $this->subPermissionNameFromRoute($this->sanitizeBaseUrl($request->route()->uri()), $request->route()->methods()[0]);
        $routeIsPermissionable = $this->routeIsPermissionable(uri: $uri, routeName: $route?->getName());
        if (
            (
                $routeIsPermissionable && !(bool) $this->authUser()
            ) || (
                $routeSubPermissionName != null
                && !\in_array($routeSubPermissionName, $this->subPermissionsNames)
                && $routeIsPermissionable
            )
        ) {
            abort(403, 'Un-authenticated');
        }

        return $next($request);
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

    private function subPermissionNameFromRoute(string $url, $routeMethod) : string
    {
        $url = $this->sanitizeRouteModelIdentifier($url);

        return "{$url}_" . self::ROUTE_METHOD_MAP[$routeMethod];
    }

    /**
     *
     * check routes against permissions config ['baseUrls', 'excluded routes']
     */
    public function routeIsPermissionable(string $uri, ?string $routeName) : bool
    {
        if (in_array($routeName, $this->excludedRoutes)) {
            return false;
        }
        for ($i = 0; $i < \count($this->baseUrls) ; $i++) {
            if (Str::contains($uri, $this->baseUrls[$i])) {
                return true;
            }
        }

        return false;
    }

    public function sanitizeRouteModelIdentifier(string $url) : string
    {
        $segments = collect(\explode('/', $url));
        $segments = $segments->filter(function ($segment) {
            return ! Str::contains($segment, '{');
        })->flatten()->toArray();

        return \implode('/', $segments);
    }
}
