<?php
namespace Gui\Forms\Validators;

use Demeter\Support\Str;

class StringValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addOption('maxLength');
        $this->addOption('minLength');
        $this->addOption('replaceWordChars', true);
        $this->addOption('removeHtml', true);

        $this->setOption('emptyValue', "");
        $this->setOption('trim', true);

        $this->setMessage('maxLength', 'gui::validation.string.max_length');
        $this->setMessage('minLength', 'gui::validation.string.min_length');
    }

    /**
     * @inheritDoc
     */
    public function clean(mixed $v) : mixed
    {
        if(is_array($v)){
            $v = implode('', $v);
        }

        return parent::clean($v);
    }

    /**
     * @inheritDoc
     * @return string
     */
    protected function validate(mixed $v) : string
    {
        $v = Str::cleanup((string) $v, $this->getOption('trim'), $this->getOption('removeHtml'), $this->getOption('replaceWordChars'));

        if($this->hasOption('maxLength') && Str::length($v) > $this->getOption('maxLength')){
            throw new Error($this, 'maxLength', ['value' => $v, 'max_length' => $this->getOption('maxLength')]);
        }

        if($this->hasOption('minLength') && Str::length($v) < $this->getOption('minLength')){
            throw new Error($this, 'minLength', ['value' => $v, 'min_length' => $this->getOption('minLength')]);
        }

        return $v;
    }
}