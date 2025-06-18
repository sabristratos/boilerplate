<?php

namespace App\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class CrudServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('crud.configurations', function () {
            $filesystem = new Filesystem();
            $configPath = app_path('Crud/Configurations');
            $configs = [];

            if (!$filesystem->isDirectory($configPath)) {
                return [];
            }

            foreach ($filesystem->files($configPath) as $file) {
                $class = 'App\\Crud\\Configurations\\' . $file->getFilenameWithoutExtension();

                if (!class_exists($class)) {
                    continue;
                }

                $reflection = new ReflectionClass($class);
                if ($reflection->isInstantiable() && $reflection->implementsInterface(\App\Crud\CrudConfigInterface::class)) {
                    $configInstance = new $class();
                    $baseName = str_replace('CrudConfig', '', $file->getFilenameWithoutExtension());
                    $alias = \Illuminate\Support\Str::plural(strtolower($baseName));
                    $configs[$alias] = $class;
                }
            }
            return $configs;
        });
    }

    public function boot(): void
    {
        Route::bind('alias', function ($value) {
            $configs = app('crud.configurations');
            if (! isset($configs[$value])) {
                throw new InvalidArgumentException("CRUD configuration for '{$value}' not found.");
            }
            // Bind the resolved class string to a new parameter.
            Route::current()->setParameter('crud_config_class', $configs[$value]);
            return $value; // Return the alias itself.
        });
    }
} 