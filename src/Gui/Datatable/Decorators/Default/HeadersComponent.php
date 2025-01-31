<?php
namespace Gui\Datatable\Decorators\Default;

use Gui\Datatable\Engines\AbstractEngine;
use Gui\Datatable\Options;
use Illuminate\Http\Request;

class HeadersComponent extends AbstractComponent
{
    /** CSS variables */
    protected string $classLine = 'headers';
    protected string $classHiddenResponsive = 'hidden-responsive';
    protected string $classSortingWait = 'sorting-waiting';
    protected string $classSortingAsc = 'sorting-asc';
    protected string $classSortingDesc = 'sorting-desc';
    protected string $classSortingInactive = 'sorting-inactive';

    /**
     * Render headers
     *
     * @return string
     */
    public function render(): string
    {
        $output = "";
        $hidden = true;

        foreach($this->getParent()->getEngine()->getColumns() as $column => $label){
            $options = $this->getParent()->getEngine()->getColumnOptions($column);
            $attributes = [];

            if($options->css()){
                $attributes['class'] = $options->css();
            }

            if($options->sort() === false){
                $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' ' . $this->classHiddenResponsive : $this->classHiddenResponsive;
            } else {
                $hidden = false;
            }

            $output.= content_tag('th', $this->renderHeaderCell($column, $label, $options), $attributes);
        }

        if($hidden){
            $this->classLine.= ' ' . $this->classHiddenResponsive;
        }

        return content_tag('tr', $output, ['class' => $this->classLine]);
    }

    /**
     * Render header cell
     *
     * @param string $column
     * @param string $label
     * @param Options $options
     * @return string
     */
    protected function renderHeaderCell(string $column, string $label, Options $options): string
    {
        $datatable = $this->getParent()->getEngine();
        $attributes = [];

        if($options->sort() === false){
            return content_tag('div', trans($label), ['class' => $this->classSortingInactive]);
        }

        if($datatable->getSortBy() === $column){
            $property = "classSorting" . ucfirst($datatable->getSortDirection());
            $attributes['class'] = $this->{$property};
            $url = $this->getParent()->url(null, $column, $datatable->getSortDirection() === AbstractEngine::SORT_DIRECTION_DESC ? AbstractEngine::SORT_DIRECTION_ASC : AbstractEngine::SORT_DIRECTION_DESC);
        } else {
            $attributes['class'] = $this->classSortingWait;
            $url = $this->getParent()->url(null, $column, $options->defaultSort());
        }

        return lr($label, $url, $datatable->getUid(), $attributes, ['method' => Request::METHOD_POST]);
    }
}