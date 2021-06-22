<?php

namespace Juicebox\Apistacker;

use Illuminate\Support\ServiceProvider;
use Juicebox\Apistacker\Commands\InstallCommand;

class ApistackerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfigs();

        if ($this->app->runningInConsole()) {
            $this->registerPublishables();
            $this->registerConsoleCommands();
        }
    }

    /**
     * Register the package publishables.
     */
    protected function registerPublishables()
    {
        $this->publishesToGroups([
            __DIR__ . '/../app/Http/Controllers/SanctumController.php' => app_path('Http/Controllers/SanctumController.php'),
            __DIR__ . '/../app/Http/Middleware/ForceJsonResponse.php' => app_path('Http/Middleware/ForceJsonResponse.php'),
        ], ['apistacker', 'apistacker-controllers']);

        $this->publishesToGroups([
            __DIR__ . '/../resources/docs' => resource_path('docs'),
        ], ['apistacker', 'apistacker-docs']);

        $this->publishesToGroups([
            __DIR__ . '/../postman' => base_path('postman'),
        ], ['apistacker', 'apistacker-postman']);
    }

    protected function publishesToGroups(array $paths, $groups = null)
    {
        if (is_null($groups)) {
            $this->publishes($paths);
            return;
        }

        foreach ((array) $groups as $group) {
            $this->publishes($paths, $group);
        }
    }
}
