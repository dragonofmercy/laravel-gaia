<?php
namespace Demeter\Providers;

use Illuminate\Support\ServiceProvider;

class DemeterServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerFlash();
    }

    /**
     * Register flash system
     *
     * @return void
     */
    protected function registerFlash() : void
    {
        $this->app->singleton(\Demeter\Support\Flash::class);
    }

    /**
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot(): void
    {
        $source = realpath(__DIR__ . '/../../../config/demeter.php');
        $this->mergeConfigFrom($source, 'demeter');
        $this->publishes([$source => $this->app->configPath('demeter.php')]);

        $this->extendHashToOpenSSL();
    }

    /**
     * Extend hash to openssl
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function extendHashToOpenSSL() : void
    {
        $this->app->make('hash')->extend('demeter.openssl', function(){
            return new \Demeter\Hashing\Openssl();
        });
    }
}
