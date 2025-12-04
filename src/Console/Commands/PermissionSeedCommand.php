<?php

namespace Mabrouk\Permission\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\RoleableSeeder;

class PermissionSeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:seed';

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

        $currentTranslationNamespace = config('translatable.translation_models_path');

        config(['translatable.translation_models_path' => 'Mabrouk\Permission\Models']);

        $seedOptions = ['--class' => RoleableSeeder::class];

        if (config('permissions.force_seeding_without_questions')) {
            $seedOptions['--force'] = true;
        }

        $this->call('db:seed', $seedOptions);

        config(['translatable.translation_models_path' => $currentTranslationNamespace]);

        self::forgetCachedSubPermissions();

        return Command::SUCCESS;
    }

    private static function forgetCachedSubPermissions(): void
    {
        $roleableModels = config('permissions.roleable_models');

        foreach ($roleableModels as $roleableModel) {
            $roleableModel::whereHas('roles')->each(function ($model) {
                $model->invalidateCachedSubPermissionNames();
            });
        }
    }
}
