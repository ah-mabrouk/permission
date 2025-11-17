<?php

namespace Mabrouk\Permission\Http\Controllers;

use Mabrouk\Permission\Models\PermissionGroup;
use Mabrouk\Permission\Http\Resources\PermissionGroupResource;
use Mabrouk\Permission\Http\Requests\PermissionGroupStoreRequest;
use Mabrouk\Permission\Http\Requests\PermissionGroupUpdateRequest;

class PermissionGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware(\Illuminate\Routing\Middleware\SubstituteBindings::class);
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $paginationLength = pagination_length(PermissionGroup::class);
        $permissionGroups = PermissionGroup::paginate($paginationLength);

        return PermissionGroupResource::collection($permissionGroups);
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(PermissionGroupStoreRequest $request)
    {
        $permissionGroup = $request->storePermissionGroup();

        return response([
            'message' => __('mabrouk/permission/permission_groups.store'),
            'permission_group' => new PermissionGroupResource($permissionGroup),
        ]);
    }

    /**
     * Display the specified resource.
     *
     */
    public function show(PermissionGroup $permissionGroup)
    {
        return response([
            'permission_group' => new PermissionGroupResource($permissionGroup),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(PermissionGroupUpdateRequest $request, PermissionGroup $permissionGroup)
    {
        $permissionGroup = $request->updatePermissionGroup();

        return response([
            'message' => __('mabrouk/permission/permission_groups.update'),
            'permission_group' => new PermissionGroupResource($permissionGroup),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(PermissionGroup $permissionGroup)
    {
        if ($permissionGroup->permissions()->count() > 0) {
            return response([
                'message' => __('mabrouk/permission/permission_groups.cant_destroy'),
            ], 409);
        }

        $permissionGroup->deleteTranslations()->delete();
        return response([
            'message' => __('mabrouk/permission/permission_groups.destroy'),
        ]);
    }
}
