<?php

return [
    'store' => 'Role created successfully',
    'update' => 'Role updated successfully',
    'destroy' => 'Role deleted successfully',
    'cant_destroy' => 'Role can\'t be deleted while it have related users',
    'cant_destroy_super_admin_role' => 'Super admin role can\'t be deleted',

    'attributes' => [
        'name' => 'role name',
        'description' => 'role description',
        'permissions' => 'permissions',
        'permission' => 'permission',
        'sub_permissions' => 'sub permissions',
        'sub_permission' => 'sub permission',
    ],
];
