# Mabrouk/Permission

mabrouk/permission is a Laravel api package for dealing with permissions.

## Table of Content
[Important Introduction](#important-introduction)

[Usage sequence](#usage-sequence)

[Installation](#Installation)

## Important introduction

In order to get the most benefit of this package results, try to follow the standard routes naming to have a well organized permissions names suites very well with your models naming as well.

* avoid using verbs in routes naming such as get_users and get_news, instead use the model plural name will fit very well here trust me.

## Usage sequence

> After installation and modifing configuration:

* add new routes -if you don't have some- which have one of the basic urls in config file
* run command ```php artisan permission:seed``` when you need to apply permissions on newly added routes.
* include predefined routes which control permission groups, Roles, and permission display names in your api documentation to make it available for implementation from frontend developer working on admin-panel or any dashboard you specified its api base url in ```permissions.php``` config file. or guide frontend developer to this documentation [Frontend Api Integration](#frontend-api-integration) section

## Installation

You can install the package using composer.

```bash
composer install mabrouk/permission
```

In order to get things work, add the ```PermissionOfficerMiddleware``` at the end of the ```$routeMiddleware``` property of ```app/Http/Kernel.php``` class

```php
    protected $routeMiddleware = [
        // ...
        'permission-officer' => \Mabrouk\Permission\Http\Middleware\PermissionOfficerMiddleware::class,
    ];
```

After this you may accept it on any specific grouped routes like ```api``` group under ```$middlewareGroups``` property in same ```kernel``` file or use it on specific routes in your routes file as most of middlewares you used to use before.

The first option to apply it to ```api``` group for example will be more comfortable during development process as you will not have to think about it anymore after configuring package configuration with your project needs.

```php
    protected $middlewareGroups = [
        'api' => [
            // ...
            'permission-officer',
        ],
    ];
```

* Now you need to run the following ```command``` in order to migrate package tables and publish ```permissions.php``` config file to config directory

```bash
php artisan permission:setup
```

## Configurations according to project needs

Config file have several configuration options and already have enough comments to describe every key meaning and how to use.

You may access it under ```config/permissions.php```

> After modifying ```permissions.php``` config file don't forget to run below command:

```bash
php artisan config:cache
```

* You are all done with installation and structure. Now we need just to understand how to use it.

## Out of the box models

We have 4 basic models to deal with:

- ```PermissionGroup``` have full crud with translatable name
- ```Permission``` only index, // show and update of translatable description and display name only. It depends on your project models names.
- ```SubPermission``` with no separate crud functionality. usually it's one of (view, create, edit, delete).
- ```Role``` have full crud with translatable name and description

## Out of the box routes

Let's run the ```route:list``` command and discover our package predefined routes

```bash
php artisan route:list
```
Actually we will find the output of the following routes in addition to your project current routes:

```php
Route::apiResource('permission-groups', PermissionGroupController::class);
Route::apiResource('permissions', PermissionController::class, ['except', ['store', 'destroy']]);
Route::apiResource('roles', RoleController::class);
```

> Show, update and destroy routes accept model ```id``` as model segment in url

> If above routes is not exists you may need to clear cached route using command ```php artisan route:clear```

## What else?

You are one step away from basic handling your project permissions with only running below command after adding any additional routes under specified base urls defined in permissions.php config file.

#### Note:
> You need to run below command after adding any new routes related to namespaces you specified in config file in order to add its suitable permissions.

```bash
php artisan permission:seed
```

> Now you will find that specified objects in config file have full permissions.

> Sub Permissions will be added depending on your routes available actions. For example if you specified actions of api resource route to allow just store and destroy for example it will affect added sub permissions accordingly, otherwise it will add the 4 actions to super admin user to play with it according to specific role he is modifying.

## Models Api Resources to expect in requests response

- PermissionGroupResource returned in all permission-groups requests except index
```php
<?php

namespace Mabrouk\Permission\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => PermissionResource::collection($this->permissions),
        ];
    }
}
```

- PermissionGroupSimpleResource returned in permission-groups index request

```php
<?php

namespace Mabrouk\Permission\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionGroupSimpleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
```

- PermissionResource

```php
<?php

namespace Mabrouk\Permission\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->display_name,
            'selected' => $this->isSelected,
            'group' => new PermissionGroupSimpleResource($this->group),
            'sub_permissions' => SubPermissionResource::collection($this->subPermissions),
        ];
    }
}
```

- SubPermissionResource

```php
<?php

namespace Mabrouk\Permission\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubPermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->display_name,
            'selected' => $this->isSelected,
        ];
    }
}
```

- RoleResource

```php
<?php

namespace Mabrouk\Permission\Http\Resources;

use Mabrouk\Permission\Models\PermissionGroup;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permission_groups' => PermissionGroupResource::collection(PermissionGroup::all()),
        ];
    }
}
```

- RoleSimpleResource

```php
<?php

namespace Mabrouk\Permission\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleSimpleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
```

## Any thing else?
Actually one more thing to know is that this package depend on [mabrouk/translatable](https://github.com/ah-mabrouk/Translatable) package in order to handle translation dynamically for any chosen language.

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License

Mabrouk/Permission package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
