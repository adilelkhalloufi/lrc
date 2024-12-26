<?php
declare(strict_types=1);
namespace Lrc;

use Illuminate\Support\ServiceProvider;
use Lrc\Commands\CommandConstants;

class LRCServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([CommandConstants::class]);
            \Log::info('*************************** Command Constants command registered.');
        }
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        // Optionally, you can publish configuration files, etc.
    }
}
