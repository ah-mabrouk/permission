<?php

namespace Mabrouk\Permission\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PermissionPublishRoutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:publish-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the routes for the Permission package';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routesPublishSubDirectory = config('permissions.routes_publish_subdirectory');

        if (File::exists(base_path("routes/{$routesPublishSubDirectory}permission_routes.php"))) {
            $this->warn("Routes have already been published in routes/{$routesPublishSubDirectory} directory.");
            return Command::SUCCESS;
        }

        if (! $this->shouldPublishAlreadyLoadedRoutes()) {
            $this->warn('Routes publishing is aborted.');
            return Command::SUCCESS;
        }

        File::copy(
            __DIR__ . "/../../routes/permission_routes.php",
            base_path("routes/{$routesPublishSubDirectory}permission_routes.php")
        );

        $this->callSilent('vendor:publish', [
            '--provider' => 'Mabrouk\Permission\PermissionServiceProvider',
        ]);

        $this->info('Routes have been published successfully.');

        return Command::SUCCESS;
    }

    private function shouldPublishAlreadyLoadedRoutes(): bool
    {
        if (config('permissions.load_routes')) {
            return $this->confirm(
                'Loading routes is enabled in the configuration file. Do you want to publish them anyway?',
                false
            );
        }
         return true;
    }
}