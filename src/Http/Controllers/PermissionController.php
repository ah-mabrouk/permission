<?php

namespace Mabrouk\Permission\Http\Controllers;

use Mabrouk\Permission\Models\Permission;
use Mabrouk\Permission\Http\Resources\PermissionResource;
use Mabrouk\Permission\Http\Requests\PermissionUpdateRequest;

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
     * @param  \Mabrouk\Permission\Models\Permission  $permission
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
     * @param  Mabrouk\Permission\Http\Requests\PermissionUpdateRequest  $request
     * @param  \Mabrouk\Permission\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(PermissionUpdateRequest $request, Permission $permission)
    {
        $permission = $request->updatePermission();
        return response([
            'message' => __('mabrouk/permission/permissions.update'),
            'permission' => new PermissionResource($permission),
        ]);
    }
}
