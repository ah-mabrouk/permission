<?php

namespace Mabrouk\RolePermissionGroup\Http\Controllers;

use Mabrouk\RolePermissionGroup\Models\Permission;
use Mabrouk\RolePermissionGroup\Http\Resources\PermissionResource;
use Mabrouk\RolePermissionGroup\Http\Requests\PermissionUpdateRequest;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paginationLength = pagination_length('Permission');
        $permissions = Permission::paginate($paginationLength);
        return PermissionResource::collection($permissions);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Mabrouk\RolePermissionGroup\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        return response([
            'permission' => new PermissionResource($permission),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Mabrouk\RolePermissionGroup\Http\Requests\PermissionUpdateRequest  $request
     * @param  \Mabrouk\RolePermissionGroup\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionUpdateRequest $request, Permission $permission)
    {
        $permission = $request->updatePermission();
        return response([
            'message' => __('mabrouk/role_permission_group/permissions.update'),
            'permission' => new PermissionResource($permission),
        ]);
    }
}
