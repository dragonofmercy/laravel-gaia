<?php
namespace Gui\Exceptions;

use Illuminate\Support\Facades\View as ViewFacade;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    /**
     * @inheritDoc
     * @return void
     */
    protected function registerErrorViewPaths(): void
    {
        ViewFacade::replaceNamespace('errors', collect(config('view.paths'))->map(function($path){
            return "$path/errors";
        })->push(__DIR__.'/views')->all());
    }
}