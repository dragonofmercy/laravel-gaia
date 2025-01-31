<?php
namespace Gui\Datatable\Decorators;

use Demeter\Support\Str;
use Gui\Datatable\Engines\AbstractEngine;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\Route as RouteFacade;

abstract class AbstractDecorator
{
    /**
     * Datatable layout
     *
     * @var string
     */
    protected string $layout;

    /**
     * Engine object
     *
     * @var AbstractEngine
     */
    protected AbstractEngine $engine;

    /**
     * Constructor.
     *
     * @param AbstractEngine $engine
     */
    public function __construct(AbstractEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Get datatable engine
     *
     * @return AbstractEngine
     */
    public function getEngine(): AbstractEngine
    {
        return $this->engine;
    }

    /**
     * Render decorator
     *
     * @return string
     */
    public function render(): string
    {
        $this->engine->make();

        $replacements = array_map(function($classname){
            return (new $classname($this))->render();
        }, $this->getComponents());

        return str_replace(array_keys($replacements), array_values($replacements), $this->layout);
    }

    /**
     * Get clean http query
     *
     * @param string $filter
     * @return array
     */
    protected function getHttpQuery(string $filter = 'dt_'): array
    {
        return collect(RequestFacade::all())->filter(function(mixed $value, string $key) use ($filter){
            return !Str::startsWith($key, $filter);
        })->toArray();
    }

    /**
     * Build url for datatable
     *
     * @param int|null $page
     * @param string|null $sortBy
     * @param string|null $sortDirection
     * @return string
     */
    public function url(int|null $page = null, string|null $sortBy = null, string|null $sortDirection = null): string
    {
        $engine = $this->getEngine();
        $httpQuery = $this->getHttpQuery();

        $httpQuery['dt_u'] = $engine->getUid();
        $httpQuery['dt_p'] = $page ?? $engine->getCurrentPage();
        $httpQuery['dt_s'] = $sortBy ?? $engine->getSortBy();
        $httpQuery['dt_o'] = $sortDirection ?? $engine->getSortDirection();

        return RouteFacade::getCurrentRoute()->uri() . "?" . http_build_query($httpQuery);
    }

    /**
     * Get reset url
     *
     * @param bool $detachSearchOnly
     * @return string
     */
    public function resetUrl(bool $detachSearchOnly = false): string
    {
        $httpQuery = $this->getHttpQuery($detachSearchOnly ? 'dt_f' : 'dt_');
        $httpQuery['dt_u'] = $this->getEngine()->getUid();
        $httpQuery['dt_c'] = 1;

        return RouteFacade::getCurrentRoute()->uri() . "?" . http_build_query($httpQuery);
    }

    /**
     * Get rendering components
     *
     * @return array
     */
    abstract protected function getComponents(): array;
}