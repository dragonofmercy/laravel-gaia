<?php
namespace Gui\Providers;

use Illuminate\Support\Facades\Blade as BladeFacade;
use Illuminate\Support\ServiceProvider;

class GuiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerConfig();
        $this->registerBladeComponents();
        $this->registerViewComposer();

        \Illuminate\Cookie\Middleware\EncryptCookies::except('dark-mode');

        $this->loadTranslationsFrom(__DIR__ . '/../Translation/lang', 'gui');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'gui');

        $this->app->singleton(\Gui\Translation\Countries::class);
    }

    public function boot(): void
    {
        if(app()->runningInConsole()){
            $this->publishConfig();
        }
    }

    protected function registerBladeComponents(): void
    {
        BladeFacade::componentNamespace('Gui\\View\\Components', 'gui');
    }

    protected function registerViewComposer(): void
    {
        $this->app['view']->composer(config('gui.view_composer_layout', 'gui::layout'), \Gui\View\Composer::class);
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../../config/gui.php', 'gui');
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../../../config/gui.php' => config_path('gui.php')
        ], 'gui-config');

        $this->publishes([
            __DIR__ . '/../resources/public' => public_path('assets/gui')
        ], 'public');
    }
}
