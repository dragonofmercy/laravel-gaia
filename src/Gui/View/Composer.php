<?php
namespace Gui\View;

use Illuminate\Support\Facades\View as ViewFacade;

class Composer
{
    public function compose(): void
    {
        $this->injectMetas();
        $this->injectLinks();
        $this->injectScripts();
    }

    /**
     * Inject metas from GUI config file
     *
     * @return void
     */
    protected function injectMetas(): void
    {
        ViewFacade::inject('title', config('gui.title'));
        ViewFacade::startPush('metas', tag('meta', ['name' => 'csrf-token', 'content' => csrf_token()]));

        foreach(config('gui.metas', []) as $name => $content){
            ViewFacade::startPush('metas', tag('meta', ['name' => $name, 'content' => $content]));
        }

        foreach(config('gui.metas_http', []) as $name => $content){
            ViewFacade::startPush('metas', tag('meta', ['http-equiv' => $name, 'content' => $content]));
        }

        foreach(config('gui.metas_properties', []) as $name => $content){
            ViewFacade::startPush('metas', tag('meta', ['property' => $name, 'content' => $content]));
        }
    }

    /**
     * Inject links from GUI config file
     *
     * @return void
     */
    protected function injectLinks(): void
    {
        $darkmode = gui_darkmode();
        $darkmodeReplacements = config('gui.darkmode_replacements', false);

        foreach(config('gui.links', []) as $link){
            if($darkmode && is_array($darkmodeReplacements) && array_key_exists($link, $darkmodeReplacements)){
                $link = $darkmodeReplacements[$link];
            }

            ViewFacade::startPush('links', tag('link', ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => $link]));
        }
    }

    /**
     * Inject scripts from GUI config file
     *
     * @return void
     */
    protected function injectScripts(): void
    {
        ViewFacade::startPush('scripts', content_tag('script', 'const GUI_LANGUAGE = { languageCode: "' . app()->currentLocale() . '", guiCopyNotification: "' . trans('gui::messages.component.copy.notify') . '" };', ['type' => 'text/javascript']));

        foreach(config('gui.scripts', []) as $script){
            ViewFacade::startPush('scripts', content_tag('script', '', ['type' => 'text/javascript', 'src' => $script]));
        }

        foreach(config('gui.deferred_script', []) as $script){
            ViewFacade::startPush('deferred-script', content_tag('script', '', ['type' => 'text/javascript', 'defer' => null, 'src' => $script]));
        }
    }
}