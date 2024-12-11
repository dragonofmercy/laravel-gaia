<?php
namespace Gui\Datatable\Decorators\Default;

class StylesComponent extends AbstractComponent
{
    /**
     * Render css styles
     *
     * @return string
     */
    public function render(): string
    {
        $output = "";
        $columnIndex = 1;
        $engine = $this->getParent()->getEngine();
        $options = $engine->getColumnsOptions();

        foreach($engine->getColumns() as $column => $label){
            if($options->has($column)){
                $columnOptions = $options->get($column);

                if($columnOptions->width()){
                    $output.= '#' . $engine->getUid() . ' .table thead th:nth-of-type(' . $columnIndex . '){width:' . $columnOptions->width() . '}';
                }

                if($columnOptions->minWidth()){
                    $output.= '#' . $engine->getUid() . ' .table thead th:nth-of-type(' . $columnIndex . '){min-width:' . $columnOptions->minWidth() . '}';
                }
            }

            $output.= '#' . $engine->getUid() . ' .table tbody td:nth-of-type(' . $columnIndex . '):before{content:"' . strip_tags(trans($label)) . '"}';
            $columnIndex++;
        }

        return strlen($output) ? content_tag('style', preg_replace('/\s+/', ' ', $output), ['type' => 'text/css']) : "";
    }
}