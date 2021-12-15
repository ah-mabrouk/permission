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
    private $packageMigrations = [
        'create_permission_groups_table',
        'create_permission_group_translations_table',
        'create_permissions_table',
        'create_permission_translations_table',
        'create_sub_permissions_table',
        'create_sub_permission_translations_table',
        'create_roles_table',
        'create_role_translations_table',
        'create_permission_role_table',
        'create_permission_role_sub_permission_table',
        'create_roleables_table',
    ];

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

            /**
             * Config and static translations
             */
            $this->publishes([
                __DIR__ . '/config/permissions.php' => config_path('permissions.php'), // ? Config
                __DIR__ . '/resources/lang' => resource_path('lang'), // ? Static translations
            ]);

            $this->app->make(Router::class)
                ->aliasMiddleware('permission-officer', PermissionOfficerMiddleware::class);
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

        foreach ($this->packageMigrations as $migrationName) {
            if (! $this->migrationExists($migrationName)) {
                $migrationFiles[__DIR__ . "/database/migrations/{$migrationName}.php.stub"] = database_path('migrations/' . date('Y_m_d_His', time()) . "_{$migrationName}.php");
            }
        }
        return $migrationFiles;
    }

    protected function migrationExists($migrationName)
    {
        $path = database_path('migrations/');
        $files = scandir($path);
        $pos = false;
        foreach ($files as &$value) {
            $pos = strpos($value, $migrationName);
            if ($pos !== false) return true;
        }
        return false;
    }
}
