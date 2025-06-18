<?php

namespace App\Providers;

use App\Crud\CrudConfigInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CrudServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('crud.configurations', function (Application $app) {
            $configs = [];
            $path = app_path('Crud/Configurations');

            if (File::isDirectory($path)) {
                $files = File::files($path);

                foreach ($files as $file) {
                    $class = 'App\\Crud\\Configurations\\' . $file->getFilenameWithoutExtension();
                    if (is_subclass_of($class, CrudConfigInterface::class)) {
                        $configInstance = new $class();
                        $alias = $configInstance->getAlias();
                        $configs[$alias] = $class;
                    }
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