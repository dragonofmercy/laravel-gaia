<?php
namespace Gui\Providers;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Support\Facades\Blade as BladeFacade;
use Illuminate\Support\ServiceProvider;

class GuiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerConfig();
        $this->loadTranslationsFrom(__DIR__ . '/../Translation/lang', 'gui');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'gui');
    }

    public function boot(): void
    {
        if(app()->runningInConsole()){

            $this->publishes([
                __DIR__ . '/../../../config/gui.php' => config_path('gui.php')
            ], 'gui-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => $this->app->resourcePath('views/vendor/gui'),
            ], 'gui');
        }

        EncryptCookies::except(\Gui\Support\Gui::DARK_MODE_COOKIE_NAME);

        $this->registerBladeComponents();
        $this->registerBladeDirectives();
    }

    protected function registerBladeComponents(): void
    {
        BladeFacade::componentNamespace('Gui\\View\\Components', 'gui');
    }

    protected function registerBladeDirectives(): void
    {
        BladeFacade::directive('darkTheme', function(){
            return "<?php echo \Gui\Support\Gui::isDarkMode() ? 'data-bs-theme=\"dark\"' : '' ?>";
        });
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../../config/gui.php', 'gui');
    }
}