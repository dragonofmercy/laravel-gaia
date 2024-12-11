<?php
namespace Demeter\Providers;

use Demeter\Compiler\MinifyCompiler;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\DynamicComponent;

class DemeterServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerFlash();

        if(config('demeter.minify', false)){
            $this->registerBladeMinify();
        }
    }

    /**
     * Register blade compressor
     *
     * @return void
     */
    protected function registerBladeMinify() : void
    {
        $this->app->singleton('blade.compiler', function(Container $app){
            return tap(new MinifyCompiler(
                $app['files'],
                $app['config']['view.compiled'],
                $app['config']->get('view.relative_hash', false) ? $app->basePath() : '',
                $app['config']->get('view.cache', true),
                $app['config']->get('view.compiled_extension', 'php'),
            ), function(MinifyCompiler $blade) {
                $blade->setIgnoredPaths(config('demeter.minify_ignore', ['Illuminate/Mail/resources/views', 'Illuminate/Notifications/resources/views']));
                $blade->initMinifyCompiler();
                $blade->component('dynamic-component', DynamicComponent::class);
            });
        });
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
