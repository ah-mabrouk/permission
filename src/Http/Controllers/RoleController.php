<?php

namespace Mabrouk\Permission\Http\Controllers;

use Mabrouk\Permission\Models\Role;
use Mabrouk\Permission\Filters\RoleFilter;
use Mabrouk\Permission\Http\Resources\RoleResource;
use Mabrouk\Permission\Http\Requests\RoleStoreRequest;
use Mabrouk\Permission\Http\Requests\RoleUpdateRequest;
use Mabrouk\Permission\Http\Resources\RoleSimpleResource;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(\Illuminate\Routing\Middleware\SubstituteBindings::class);
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(RoleFilter $filters)
    {
        $paginationLength = pagination_length(Role::class);
        $roles = Role::filter($filters)->paginate($paginationLength);

        return RoleSimpleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(RoleStoreRequest $request)
    {
        $role = $request->storeRole();

        return response([
            'message' => __('mabrouk/permission/roles.store'),
            'role' => new RoleResource($role),
        ]);
    }

    /**
     * Display the specified resource.
     *
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
     */
    public function update(RoleUpdateRequest $request, Role $role)
    {
        $role = $request->updateRole();

        return response([
            'message' => __('mabrouk/permission/roles.update'),
            'role' => new RoleResource($role),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(Role $role)
    {
        $role = $role->remove();

        return response([
            'message' => $role->response['message'],
        ], $role->response['response_code']);
    }
}
