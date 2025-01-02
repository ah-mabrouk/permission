<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => array_unique(array_merge(config('permissions.middlewares'), [
        'auth:api',
        'permission-officer',
        'translatable',
    ]))
], function () {
    Route::apiResource('permission-groups', PermissionGroupController::class);
    Route::apiResource('permissions', PermissionController::class, ['except', ['store', 'destroy']]);
    Route::apiResource('roles', RoleController::class);
});