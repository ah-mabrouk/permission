<?php

namespace Mabrouk\Permission\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\RoleableSeeder;
use Illuminate\Support\Facades\File;

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
        $this->call('config:cache');

        $this->call('db:seed', ['--class' => RoleableSeeder::class]);
        config(['translatable.translation_models_path' => $currentTranslationNamespace]);

        $this->call('config:cache');

        return Command::SUCCESS;
    }
}
