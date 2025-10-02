<?php

namespace Gui\Forms\Elements;

class InputPassword extends InputText
{
    public static string $stringGeneratePassword = "gui::messages.component.password.new";
    public static string $stringChoose = "gui::messages.component.password.choose";
    public static string $stringChooseAndCopy = "gui::messages.component.password.choose_copy";

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.input-password';
    }

    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('display', true);
        $this->addOption('generator', false);
        $this->addOption('min', 8);
        $this->addOption('max', 32);
        $this->addOption('chars', 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.-@');
        $this->addOption('regex', '^[A-Za-z0-9]+(?=.*[0-9])(?=.*[a-z])(?=.*[.@-])(?=.*[A-Z]).*[A-Za-z0-9]+$');
        $this->addOption('copyInField', 'auto');

        $this->setAttribute('type', 'password');
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->setViewVar('stringGeneratePassword', trans(static::$stringGeneratePassword));
        $this->setViewVar('stringChoose', trans(static::$stringChoose));
        $this->setViewVar('stringChooseAndCopy', trans(static::$stringChooseAndCopy));

        $this->setViewVar('optionsDisplay', $this->getOption('display'));
        $this->setViewVar('optionsGenerator', $this->getOption('generator'));

        $componentConfig = [
            'min' => $this->getOption('min'),
            'max' => $this->getOption('max'),
            'chars' => $this->getOption('chars'),
            'regex' => $this->getOption('regex'),
            'copyInField' => $this->getOption('copyInField'),
        ];

        $this->setViewVar('componentConfig', json_encode($componentConfig));
    }
}