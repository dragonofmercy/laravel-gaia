<?php
namespace Gui\Datatable\Decorators\Default;

use Gui\Datatable\Filters\AbstractFilter;

class SearchComponent extends AbstractComponent
{
    const SEARCH_FUNCTION = 'datagrid-search';
    const SEARCH_FUNCTION_KEYDOWN = 'datagrid-search-keydown';

    /**
     * Javascript functions map
     * @var array<string, string>
     */
    public static array $javascriptSearchFunctions = [
        self::SEARCH_FUNCTION => "gui.datagridSearch('%s', '%s', %s)",
        self::SEARCH_FUNCTION_KEYDOWN => "gui.datagridSearchKeydown('%s', '%s', event)",
    ];

    /**
     * Form UID format
     * @var string
     */
    protected string $formUid = 'datagrid_search_%s';

    /**
     * Search global layout
     * @var string
     */
    protected string $layout = <<<EOF
<div class="collapse" id="{search_id}">
    <div class="datatable-search">
        <div class="row-exp">
            <div class="col-exp">{content}</div>
            <div class="col-exp">{buttons}</div>
        </div>
    </div>
</div>
EOF;

    /**
     * Search group layout
     * @var string
     */
    protected string $layoutSearchGroup = <<<EOF
<div class="search-group">
    <div class="{search_class}">
        <div class="control-label"><label>{search_label}</label></div>
        <div class="control-field">{search_field}</div>
    </div>
</div>
EOF;

    /**
     * Search button layout
     * @var string
     */
    protected string $layoutSearchButtons = <<<EOF
<div class="search-buttons">{button_search}{button_reset}{button_clear}</div>
EOF;

    /**
     * Render search
     *
     * @return string
     */
    public function render(): string
    {
        if(!$this->hasSearchFilters()){
            return "";
        }

        $output = "";
        $engine = $this->getParent()->getEngine();

        foreach($engine->getSearchFilters() as $column => $filter){
            $output.= $this->renderSearchGroup($column, $filter);
        }

        if($engine->getSearchValues()->count() || $engine->getDisplaySearch()){
            if($engine->getDisplaySearch()){
                $this->layoutSearchButtons = str_replace('{button_clear}', '', $this->layoutSearchButtons);
            }
            $this->layout = preg_replace('/(class=".*)(collapse)/iU', '$1collapse show', $this->layout);
        }

        $replacements = [
            '{search_id}' => sprintf($this->formUid, $this->getParent()->getEngine()->getUid()),
            '{buttons}' => $this->renderButtons(),
            '{content}' => $output
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $this->layout);
    }

    /**
     * Get formated field name
     *
     * @param ...$name
     * @return string
     */
    public function formatFieldName(...$name): string
    {
        return vsprintf("dt_f" . str_repeat('[%s]', count($name)), $name);
    }

    /**
     * Get if datatable has search filters
     * @return bool
     */
    protected function hasSearchFilters(): bool
    {
        return $this->getParent()->getEngine()->getSearchFilters()->count() > 0;
    }

    /**
     * Get javascript function by key
     * @param string $key
     * @return string
     */
    public function getJavascriptFunction(string $key): string
    {
        if(!array_key_exists($key, static::$javascriptSearchFunctions)){
            throw new \InvalidArgumentException("Javascript function with key [$key] was not found");
        }

        return static::$javascriptSearchFunctions[$key];
    }

    /**
     * Render buttons
     *
     * @return string
     */
    protected function renderButtons(): string
    {
        $parent = $this->getParent();

        $replacements = [
            '{button_search}' => content_tag('button', content_tag('i', '', ['class' => 'fa-solid fa-magnifying-glass']) . trans('gui::messages.datatable.search'), ['class' => 'btn btn-default btn-icon inline', 'data-search' => 1, 'data-loading-text' => trans('gui::messages.generic.loading'), 'onclick' => sprintf(static::$javascriptSearchFunctions[self::SEARCH_FUNCTION], $parent->getEngine()->getUid(), url($parent->url(1)), 'false')]),
            '{button_reset}' => content_tag('button', content_tag('i', '', ['class' => 'fa-solid fa-eraser']) . trans('gui::messages.datatable.clear'), ['class' => 'btn btn-default btn-icon inline', 'data-loading-text' => trans('gui::messages.generic.loading'), 'onclick' => sprintf(static::$javascriptSearchFunctions[self::SEARCH_FUNCTION], $parent->getEngine()->getUid(), url($parent->url()), 'true')]),
            '{button_clear}' => gui_button_link_remote('gui::messages.datatable.close', $parent->resetUrl(true), $parent->getEngine()->getUid(), 'fa-solid fa-xmark', ['class' => 'btn btn-default btn-icon inline pull-right', 'data-loading-text' => trans('gui::messages.generic.loading')], ['method' => \Illuminate\Http\Request::METHOD_POST])
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $this->layoutSearchButtons);
    }

    /**
     * Render search group
     *
     * @param string $column
     * @param AbstractFilter $filter
     * @return string
     */
    protected function renderSearchGroup(string $column, AbstractFilter $filter): string
    {
        $class = "";
        $replacements = [
            '{search_field}' => $this->renderSearchElement($column, $filter, $class),
            '{search_class}' => $class,
            '{search_label}' => trans($filter->label())
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $this->layoutSearchGroup);
    }

    /**
     * Render search element
     *
     * @param string $column
     * @param AbstractFilter $filter
     * @param string $parentClass
     * @return string
     */
    protected function renderSearchElement(string $column, AbstractFilter $filter, string &$parentClass): string
    {
        $classname = $filter->getDecorator();
        return (new $classname($this, $column, $filter, $parentClass))->render();
    }
}