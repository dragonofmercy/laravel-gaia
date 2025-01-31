<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;

class InputRange extends Input
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('progression', true);
        $this->addOption('tooltip', true);
        $this->addOption('tooltipFormat', '{0}');

        $this->addOption('min', 0);
        $this->addOption('max', 100);
        $this->addOption('step', 1);

        $this->addOption('list', []);

        $this->setOption('type', 'range');
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        $this->appendAttribute('class', 'form-range');

        if(!is_array($this->getOption('list'))){
            throw new \TypeError("TypeError: Option [list] must be an array");
        }

        $this->attributes['min'] = $this->getOption('min');
        $this->attributes['max'] = $this->getOption('max');
        $this->attributes['step'] = $this->getOption('step');

        $this->attributes['data-gui-behavior'] = 'range';
        $this->attributes['data-range-progression'] = $this->getOption('progression') ? 'true' : 'false';
        $this->attributes['data-range-tooltip'] = $this->getOption('tooltip') ? 'true' : 'false';
        $this->attributes['data-range-tooltip-format'] = $this->getOption('tooltipFormat');
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null): string
    {
        $output = "";

        if(count($this->getOption('list')) > 0){
            $this->attributes['list'] = $this->generateId($name) . '_list';
        }

        $output.= content_tag('div', parent::render($name, $value, $error), ['class' => 'gui-input-range']);

        if(count($this->getOption('list')) > 0){
            $list = "";
            collect($this->getOption('list'))->map(function(mixed $v, string $k) use (&$list){
                $list.= content_tag('option', '', ['value' => $k, 'label' => (string) $v]);
            });
            $output.= content_tag('datalist', $list, ['id' => $this->generateId($name) . '_list']);
        }

        return $output;
    }
}