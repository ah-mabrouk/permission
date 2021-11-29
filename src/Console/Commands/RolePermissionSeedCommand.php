<?php

namespace Mabrouk\RolePermissionGroup\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Mabrouk\RolePermissionGroup\Database\Seeders\RoleableSeeder;

class RolePermissionSeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role_permission_groups:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed Role Permission Groups according to current routes and configurations';

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
        $this->info('Seeding Role Permission Group package data...');

        $this->call('db:seed', ['--class' => 'Mabrouk\RolePermissionGroup\Database\Seeders\RoleableSeeder']);

        return Command::SUCCESS;
    }

    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig()
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => 'Mabrouk\RolePermissionGroup\RolePermissionGroupServiceProvider',
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

       $this->call('vendor:publish', $params);
    }
}
