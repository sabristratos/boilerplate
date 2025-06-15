<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions from policy files to the database';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting permission synchronization...');

        $policiesPath = app_path('Policies');
        $policyFiles = File::files($policiesPath);
        $existingPermissions = Permission::pluck('slug')->toArray();
        $createdCount = 0;

        foreach ($policyFiles as $file) {
            $class = 'App\\Policies\\' . $file->getFilenameWithoutExtension();

            if (!class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);
            $modelName = Str::snake(str_replace('Policy', '', $reflection->getShortName()));

            $traitMethods = [];
            foreach ($reflection->getTraits() as $trait) {
                foreach ($trait->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    $traitMethods[] = $method->getName();
                }
            }

            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                // Ignore constructor and methods from base classes/traits
                if ($method->isConstructor() || in_array($method->getName(), $traitMethods)) {
                    continue;
                }

                $permissionAction = Str::snake($method->getName());
                $permissionSlug = $modelName . '.' . $permissionAction;

                if (in_array($permissionSlug, $existingPermissions)) {
                    continue;
                }

                $permissionName = Str::of($permissionSlug)->replace('.', ' ')->title()->toString();

                Permission::create([
                    'name' => ['en' => $permissionName],
                    'slug' => $permissionSlug,
                    'description' => ['en' => 'Allows to ' . strtolower($permissionName)],
                ]);

                $this->line("Created permission: <info>{$permissionSlug}</info>");
                $createdCount++;
                $existingPermissions[] = $permissionSlug; // Add to list to avoid duplicate creation in same run
            }
        }

        if ($createdCount > 0) {
            $this->info("Successfully created {$createdCount} new permissions.");
        } else {
            $this->info('All permissions are already up to date.');
        }

        // Clear permission cache
        app(\App\Services\PermissionService::class)->clearCache();
        $this->info('Permission cache cleared.');
    }
} 