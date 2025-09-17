<?php
namespace Demeter\Providers;

use Illuminate\Support\ServiceProvider;

class DemeterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerConfig();
    }

    public function boot(): void
    {
        if(app()->runningInConsole()){
            $this->publishConfig();
        }

        $this->extendHashToOpenSSL();
    }

    protected function extendHashToOpenSSL(): void
    {
        $this->app->make('hash')->extend('demeter.openssl', function(){
            return new \Demeter\Hashing\Openssl();
        });
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../../config/demeter.php', 'demeter');
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../../../config/demeter.php' => config_path('demeter.php')
        ], 'demeter-config');
    }
}
