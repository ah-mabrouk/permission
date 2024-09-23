<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Project models which accept relations with roles
    |--------------------------------------------------------------------------
    |
    | Here you may configure your project models including namespace to apply
    | its relation with permissions echo system
    | Most of projects only deal with User model as the only model to deal with
    | permissions. But some projects may have the preference to have additional
    | model such as Admin model. so you may add any additional models here.
    |
    | It's important to consider the key name here as the relation method name
    | which allow you to access the reversed relation when you have the role
    | object and want to get it's related objects of specific type "users"
    | for example as below [key => value] pair.
    |
    */

    'roleable_models' => [
        'users' => App\Models\User::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Project owner model
    |--------------------------------------------------------------------------
    |
    | If you have multible 'roleable_models' defined in above array, then you need
    | to define where the project owner person belongs to exactly as this model
    | will have always the upper hand over the rest of specified models in
    | 'roleable_models'.
    |
    | If you only have one 'roleable_models' input then just keep it with
    | the same value in below key as well
    |
    */

    'project_owner_model' => App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Project Default Owners Accounts
    |--------------------------------------------------------------------------
    |
    | This is a list of real owners accounts grouped by their models, To understand
    | the importance behind this list, You should know that any included id
    | regarding to its model below will have all permissions auto assigned to it
    | as soon as you run "php artisan permission:seed" command, So, you don't
    | have to assign it manually each time you have a newly creaded route.
    |
    | As you can see 'ids' key accept array value of multiple ids related just to one
    | type of models as specified in 'model' key value.
    |
    */

    'project_full_permission_admins' => [
        [
            'model' => App\Models\User::class,
            'ids' => [1],
        ],
        // [
        //     'model' => App\Models\Admin::class,
        //     'ids' => [5, 10, 11],
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Package routes prefix
    |--------------------------------------------------------------------------
    |
    | Here you may prefer to union the usage of your project routes with a specific
    | prefix related to admin side or something. Define this prefered prefix here
    | and access package predefined routes under the same project admin side prefix
    | to union the output of your apis and don't confuse frontend developers
    | working on admin side
    |
    */
    'package_routes_prefix' => 'api',

    /*
    |--------------------------------------------------------------------------
    | Project base urls to be handled by permissions
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the project base urls that need to be included
    | in permissions process, It's already set to admin-panel by default but
    | you may specify any base urls that you need.
    |
    | Please note that: any base url included will need authentication for it and all nested
    | routes. So, You don't need to specify all of your routes here. You just need
    | to add base urls only.
    | e.g ['admin-panel', 'dashboard', 'company-dashboard', 'tenant-panel', ...etc];
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
    | e.g ['admin-panel/signin', 'admin-panel/notifications', ...etc];
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

    /*
    |--------------------------------------------------------------------------
    | Package migrations sub-folder
    |--------------------------------------------------------------------------
    |
    | This configuration key specifies the sub-folder within the database/migrations
    | directory where the package's migration files will be published. If the key
    | is left empty, the migration files will be published directly to the
    | database/migrations directory. If a sub-folder is specified, the migration
    | files will be published to that sub-folder.
    |
    */

    'migration_sub_folder' => '',

    /*
    |--------------------------------------------------------------------------
    | Should Run Command From Console
    |--------------------------------------------------------------------------
    |
    | This configuration key determines whether the permission commands should
    | be run from the console. By default, it is set to true, meaning the
    | commands will run from the console. You can change this value by setting
    | the PERMISSION_COMMAND_SHOULD_RUN_FROM_CONSOLE environment variable.
    |
    */
];
