<?php

namespace Mabrouk\RolePermissionGroup\Http\Controllers;

use Mabrouk\RolePermissionGroup\Models\Role;
use Mabrouk\RolePermissionGroup\Admin\RoleFilter;
use Mabrouk\RolePermissionGroup\Http\Resources\RoleResource;
use Mabrouk\PermissionGroup\Http\Resources\RoleSimpleResource;
use Mabrouk\RolePermissionGroup\Http\Requests\RoleStoreRequest;
use Mabrouk\RolePermissionGroup\Http\Requests\RoleUpdateRequest;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Mabrouk\RolePermissionGroup\Admin\RoleFilter  $filters
     * @return \Illuminate\Http\Response
     */
    public function index(RoleFilter $filters)
    {
        $paginationLength = pagination_length('Role');
        $roles = Role::filter($filters)->paginate($paginationLength);
        return RoleSimpleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Mabrouk\RolePermissionGroup\Http\Requests\RoleStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleStoreRequest $request)
    {
        $role = $request->storeRole();
        return response([
            'message' => __('mabrouk/role_permission_group/roles.store'),
            'role' => new RoleResource($role),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Mabrouk\RolePermissionGroup\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return response([
            'role' => new RoleResource($role),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Mabrouk\RolePermissionGroup\Http\Requests\RoleUpdateRequest  $request
     * @param  \Mabrouk\RolePermissionGroup\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(RoleUpdateRequest $request, Role $role)
    {
        $role = $request->updateRole();
        return response([
            'message' => __('mabrouk/role_permission_group/roles.update'),
            'role' => new RoleResource($role),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Mabrouk\RolePermissionGroup\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role = $role->remove();
        return response([
            'message' => $role->response['message'],
        ], $role->response['response_code']);
    }
}
