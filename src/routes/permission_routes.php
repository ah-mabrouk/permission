<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => [
        'auth:api',
        'permission-officer',
    ]
], function () {
    Route::apiResource('permission-groups', PermissionGroupController::class);
    Route::apiResource('permissions', PermissionController::class, ['except', ['store', 'destroy']]);
    Route::apiResource('roles', RoleController::class);
});