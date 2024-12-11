<?php
namespace Gui\Providers;

use Illuminate\Support\Facades\Blade as BladeFacade;
use Illuminate\Support\ServiceProvider;

class GuiServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        \Illuminate\Cookie\Middleware\EncryptCookies::except('dark-mode');

        $this->loadTranslationsFrom(__DIR__ . '/../Translation/lang', 'gui');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'gui');

        $this->app->singleton(\Gui\Translation\Countries::class);
    }

    /**
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot(): void
    {
        $source = realpath(__DIR__ . '/../../../config/gui.php');

        $this->mergeConfigFrom($source, 'gui');
        $this->publishes([$source => $this->app->configPath('gui.php')], 'config');
        $this->publishes([__DIR__ . '/../resources/public' => public_path('assets/gui')], 'public');

        $this->registerBladeComponents();
        $this->registerViewComposer();
    }

    /**
     * Register blade components
     *
     * @return void
     */
    protected function registerBladeComponents(): void
    {
        BladeFacade::componentNamespace('Gui\\View\\Components', 'gui');
    }

    /**
     * Register view composer
     *
     * @return void
     */
    protected function registerViewComposer(): void
    {
        $this->app['view']->composer(config('gui.view_composer_layout', 'gui::layout'), \Gui\View\Composer::class);
    }
}
