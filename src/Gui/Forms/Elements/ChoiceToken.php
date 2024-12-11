<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;
use Illuminate\Support\Collection;

class ChoiceToken extends ChoiceSelect
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addOption('delimiter', ',');
        $this->addOption('maxOptions', 10);
        $this->addOption('maxItems', null);
        $this->addOption('labelField', 'text');
        $this->addOption('valueField', 'value');
        $this->addOption('sortable', false);
        $this->addOption('provider', 'select');
        $this->addOption('providerQueryParameter', 'term');
        $this->addOption('providerCache', false);
        $this->addOption('setFirstOptionActive', true);
        $this->addOption('selectOnTab', true);
        $this->addOption('openOnFocus', false);
        $this->addOption('openOnMouseDown', false);
        $this->addOption('searchConjunction', 'and');
        $this->addOption('searchRespectWordBoundaries', false);
        $this->addOption('layoutDirection', 'row');
    }

    public function validateOptions(array|Collection $options = []) : void
    {
        if(isset($options['provider']) && $options['provider'] !== 'select'){
            $options['choices'] = [];
        }

        parent::validateOptions($options);
    }

    protected function beforeRender() : void
    {
        parent::beforeRender();

        if(null === $this->getOption('maxItems') || $this->getOption('maxItems') > 1){
            $this->setOption('multiple', true);
        }

        if($this->getOption('provider') !== 'select'){
            $this->setOption('provider', url($this->getOption('provider')));
        }

        $this->setAttribute('class', 'd-none');
    }

    public function render(string $name, mixed $value = null, ?Error $error = null) : string
    {
        $opt = [];

        $opt['delimiter'] = $this->getOption('delimiter');
        $opt['maxOptions'] = $this->getOption('maxOptions');
        $opt['maxItems'] = $this->getOption('maxItems');
        $opt['labelField'] = $this->getOption('labelField');
        $opt['valueField'] = $this->getOption('valueField');
        $opt['provider'] = $this->getOption('provider');
        $opt['providerQueryParameter'] = $this->getOption('providerQueryParameter');
        $opt['providerCache'] = $this->getOption('providerCache');
        $opt['sortable'] = $this->getOption('sortable');
        $opt['setFirstOptionActive'] = $this->getOption('setFirstOptionActive');
        $opt['selectOnTab'] = $this->getOption('selectOnTab');
        $opt['openOnFocus'] = $this->getOption('openOnFocus');
        $opt['openOnMouseDown'] = $this->getOption('openOnMouseDown');
        $opt['searchConjunction'] = $this->getOption('searchConjunction');
        $opt['searchRespectWordBoundaries'] = $this->getOption('searchRespectWordBoundaries');
        $opt['layoutDirection'] = $this->getOption('layoutDirection');

        $js = '$("#' . $this->generateId($name) . '").GUIControlTokenize(' . _javascript_php_to_object($opt) . ')';

        $output = content_tag(name: 'div', attributes:['class' => 'form-control tokenize-container']);
        $output.= parent::render($name, $value, $error);
        $output.= javascript_tag_deferred($js);

        return $output;
    }
}