<?php

namespace Mabrouk\Permission\Http\Controllers;

use Mabrouk\Permission\Models\PermissionGroup;
use Mabrouk\Permission\Http\Resources\PermissionGroupResource;
use Mabrouk\Permission\Http\Requests\PermissionGroupStoreRequest;
use Mabrouk\Permission\Http\Requests\PermissionGroupUpdateRequest;

class PermissionGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paginationLength = pagination_length('PermissionGroup');
        $permissionGroups = PermissionGroup::paginate($paginationLength);
        return PermissionGroupResource::collection($permissionGroups);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Mabrouk\Permission\Http\Requests\PermissionGroupStoreRequest  $request
     * @return \Illuminate\Http\Response
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
     * @param  \Mabrouk\Permission\Models\PermissionGroup  $permissionGroup
     * @return \Illuminate\Http\Response
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
     * @param  Mabrouk\Permission\Http\Requests\PermissionGroupUpdateRequest  $request
     * @param  \Mabrouk\Permission\Models\PermissionGroup  $permissionGroup
     * @return \Illuminate\Http\Response
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
     * @param  \Mabrouk\Permission\Models\PermissionGroup  $permissionGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(PermissionGroup $permissionGroup)
    {
        if ($permissionGroup->permissions()->count() > 0) {
            return response([
                'message' => __('mabrouk/permission/permission_groups.cant_destroy'),
            ], 422);
        }
        $permissionGroup->deleteTranslations()->delete();
        return response([
            'message' => __('mabrouk/permission/permission_groups.destroy'),
        ]);
    }
}
