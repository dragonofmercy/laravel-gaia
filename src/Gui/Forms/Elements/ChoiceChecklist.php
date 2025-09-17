<?php
namespace Gui\Forms\Elements;

class ChoiceChecklist extends ChoiceCheckbox
{
    public static string $stringCheckAll = "gui::messages.component.checklist.all";
    public static string $stringUnCheckAll = "gui::messages.component.checklist.none";

    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('columns', 2);
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.checklist';
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        $this->setViewVar('itemsPerColumns', ceil($this->getChoices()->count() / (int) $this->getOption('columns')));
        $this->setViewVar('stringCheckAll', trans(static::$stringCheckAll));
        $this->setViewVar('stringUnCheckAll', trans(static::$stringUnCheckAll));
    }
}