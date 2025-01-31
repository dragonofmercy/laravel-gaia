<?php
namespace Gui\Datatable\Decorators\Default;

use Demeter\Support\Str;
use Illuminate\Support\Collection;

class RowsComponent extends AbstractComponent
{
    /** Translation keys */
    protected string $emptyValue = "gui::messages.generic.empty";
    protected string $emptyRowContent = "gui::messages.datatable.no_elements";

    /** CSS variables */
    protected array $colorVariations = ['datatable-row dr', 'datatable-row lt'];
    protected string $classSortedColumn = 'sorting'; // TODO: Implement sorting css class.
    protected string $classEmptyRow = 'gui-no-elements';

    /**
     * Empty row layout
     * @var string
     */
    protected string $layoutEmptyRow = '<i class="far fa-inbox"></i>{content}</div>';

    /**
     * @inheritDoc
     * @return string
     */
    public function render(): string
    {
        $rows = $this->getParent()->getEngine()->getPaginationCollection();

        if(!$rows->count()){
            return content_tag('tr', $this->renderEmpty());
        }

        return $this->renderRows($rows);
    }

    /**
     * Render empty row
     *
     * @return string
     */
    protected function renderEmpty(): string
    {
        return content_tag('td', str_replace('{content}', trans($this->emptyRowContent), $this->layoutEmptyRow), ['class' => $this->classEmptyRow, 'colspan' => $this->getParent()->getEngine()->columnsCount()]);
    }

    /**
     * Render rows
     *
     * @param Collection $rows
     * @return string
     */
    protected function renderRows(Collection $rows): string
    {
        $color = true;
        return $rows->map(function($cells) use (&$color){
            $color = !$color;
            return content_tag('tr', $this->renderCells($cells), ['class' => $this->colorVariations[(int) $color]]);
        })->implode('');
    }

    /**
     * Render row cells
     *
     * @param Collection|array $cells
     * @return string
     */
    protected function renderCells(Collection|array $cells): string
    {
        if(!$cells instanceof Collection){
            $cells = collect($cells);
        }

        return $this->getParent()->getEngine()->getColumns()->keys()->map(function(string $column) use ($cells){
            $attributes = new Collection();
            $options = $this->getParent()->getEngine()->getColumnOptions($column);
            if($options->css()){
                $attributes['class'] = $options->css();
            }
            if($this->getParent()->getEngine()->getSortBy() === $column){
                $attributes['class'] = Str::join($attributes->get('class', ""), $this->classSortedColumn);
            }
            return content_tag('td', $cells->get($column, trans($this->emptyValue)), $attributes);
        })->implode('');
    }
}