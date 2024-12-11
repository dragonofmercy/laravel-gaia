<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;

class ChoiceChecklist extends ChoiceCheckbox
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addOption('columns', 2);
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null) : string
    {
        if((int) $this->getOption('columns') === 0){
            throw new \InvalidArgumentException("Cannot set [0] to [columns] option");
        }

        $count = count($this->getOption('choices'));
        $median = ceil($count / (int) $this->getOption('columns'));

        if($count > 0){
            $inner = parent::render($name, $value, $error);
        } else {
            $inner = content_tag('div', trans('gui::messages.component.checklist.empty'), ['class' => 'text-center']);
        }

        $output = content_tag('div', l('gui::messages.component.checklist.all', options: ['data-trigger' => 'select']) . content_tag('div', '', ['class' => 'vr']) . l('gui::messages.component.checklist.none', options: ['data-trigger' => 'unselect']), ['class' => 'checklist-heading']);
        $output.= content_tag('div', $inner, ['class' => 'checklist-content', 'style' => '--gui-form-checklist-items: ' . $median]);
        $output.= javascript_tag_deferred("$('#" . $this->generateId($name) . "').GUIControlChecklist()");

        return content_tag('div', $output, ['id' => $this->generateId($name), 'class' => 'gui-control-checklist']);
    }
}