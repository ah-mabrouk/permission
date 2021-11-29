<?php

namespace Mabrouk\Permission;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mabrouk\Permission\Console\Commands\PermissionSeedCommand;
use Mabrouk\Permission\Console\Commands\PermissionSetupCommand;
use Mabrouk\Permission\Http\Middleware\PermissionOfficerMiddleware;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        require_once __DIR__ . '/Helpers/PermissionHelperFunctions.php';

        $this->registerRoutes();

        if ($this->app->runningInConsole()) {

            $this->commands([
                PermissionSetupCommand::class,
                PermissionSeedCommand::class,
            ]);

            /**
             * Migrations
             */
            $migrationFiles = $this->migrationFiles();
            if (\count($migrationFiles) > 0) {
                $this->publishes($migrationFiles, 'permission_migrations');
            }

            $router = $this->app->make(Router::class);
            $router->pushMiddlewareToGroup('api', PermissionOfficerMiddleware::class);

            /**
             * Config and static translations
             */
            $this->publishes([
                __DIR__ . '/config/permissions.php' => config_path('permissions.php'), // ? Config
                __DIR__ . '/resources/lang' => resource_path('lang'), // ? Static translations
            ]);
        }
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/permission_routes.php');
        });
    }

    protected function routeConfiguration()
    {
        return [
            'namespace' => 'Mabrouk\Permission\Http\Controllers',
            'prefix' => config('permissions.prefix'),
        ];
    }

    protected function migrationFiles()
    {
        $migrationFiles = [];

        switch (true) {
            case ! class_exists('CreatePermissionGroupsTable') :
                $migrationFiles[__DIR__ . '/database/migrations/create_permission_groups_table.php.stub'] = database_path('migrations/' . date('Y_m_d_His', time()) . '_create_permission_groups_table.php');
            case ! class_exists('CreatePermissionGroupTranslationsTable') :
                $migrationFiles[__DIR__ . '/database/migrations/create_permission_group_translations_table.php.stub'] = database_path('migrations/' . date('Y_m_d_His', time()) . '_create_permission_group_translations_table.php');
            case ! class_exists('CreatePermissionsTable') :
                $migrationFiles[__DIR__ . '/database/migrations/create_permissions_table.php.stub'] = database_path('migrations/' . date('Y_m_d_His', time()) . '_create_permissions_table.php');
            case ! class_exists('CreatePermissionTranslationsTable') :
                $migrationFiles[__DIR__ . '/database/migrations/create_permission_translations_table.php.stub'] = database_path('migrations/' . date('Y_m_d_His', time()) . '_create_permission_translations_table.php');
            case ! class_exists('CreateSubPermissionsTable') :
                $migrationFiles[__DIR__ . '/database/migrations/create_sub_permissions_table.php.stub'] = database_path('migrations/' . date('Y_m_d_His', time()) . '_create_sub_permissions_table.php');
            case ! class_exists('CreateSubPermissionTranslationsTable') :
                $migrationFiles[__DIR__ . '/database/migrations/create_sub_permission_translations_table.php.stub'] = database_path('migrations/' . date('Y_m_d_His', time()) . '_create_sub_permission_translations_table.php');
            case ! class_exists('CreateRolesTable') :
                $migrationFiles[__DIR__ . '/database/migrations/create_roles_table.php.stub'] = database_path('migrations/' . date('Y_m_d_His', time()) . '_create_roles_table.php');
            case ! class_exists('CreateRoleTranslationsTable') :
                $migrationFiles[__DIR__ . '/database/migrations/create_role_translations_table.php.stub'] = database_path('migrations/' . date('Y_m_d_His', time()) . '_create_role_translations_table.php');
            case ! class_exists('CreatePermissionRoleTable') :
                $migrationFiles[__DIR__ . '/database/migrations/create_permission_role_table.php.stub'] = database_path('migrations/' . date('Y_m_d_His', time()) . '_create_permission_role_table.php');
            case ! class_exists('CreatePermissionRoleSubPermissionTable') :
                $migrationFiles[__DIR__ . '/database/migrations/create_permission_role_sub_permission_table.php.stub'] = database_path('migrations/' . date('Y_m_d_His', time()) . '_create_permission_role_sub_permission_table.php');
            case ! class_exists('CreateRoleablesTable') :
                $migrationFiles[__DIR__ . '/database/migrations/create_roleables_table.php.stub'] = database_path('migrations/' . date('Y_m_d_His', time()) . '_create_roleables_table.php');
        }
        return $migrationFiles;
    }
}
