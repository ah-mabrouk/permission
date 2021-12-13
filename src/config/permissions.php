<?php

return [
    'roleable_models' => [
        'users' => App\Models\User::class,
    ],

    'project_owner_model' => App\Models\User::class,

    'project_owners' => [
        [
            'model' => App\Models\User::class,
            'ids' => [1],
        ],
        // [
        //     'model' => App\Models\Admin::class,
        //     'ids' => [5, 10, 11],
        // ],
    ],

    'project_owner_id' => 1,

    'routes_prefix' => 'api',

    /*
    |--------------------------------------------------------------------------
    | Project base urls to be handled by permissions
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the project base urls that need to be included
    | in permissions process, It's already set to admin-panel by default but
    | you may specify any base urls that you need.
    |
    */

    'base_urls' => [
        'permission-groups',
        'permissions',
        'roles',
        'admin-panel',
    ],

    /*
    |--------------------------------------------------------------------------
    | Project routes that will be excluded from Permissions process
    |--------------------------------------------------------------------------
    |
    | In some rare cases you may have routes that have one of the above base urls but it
    | don't need a permission to access. Here you may define this kind of routes.
    | This routes can be added using full url or just a word included so,
    | you need to carefully handle this to not miss things up.
    |
    */

    'excluded_routes' => [
        // 'notifications',
    ],

    /*
    |--------------------------------------------------------------------------
    | Project main permission group
    |--------------------------------------------------------------------------
    |
    | We depend on at least one group exists to add all auto generated permissions
    | to it as children. So, We already did that for you as default. In case you
    | would like to change it Make sure to modify the value here to reflect changes.
    |
    */

    'base_permission_group_id' => 1,
];
