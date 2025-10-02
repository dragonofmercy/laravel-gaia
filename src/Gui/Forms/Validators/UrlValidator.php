<?php

namespace Gui\Forms\Validators;

use Illuminate\Support\Str;

class UrlValidator extends StringValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('protocols', []);

        $this->setMessage('invalid', 'gui::validation.url.invalid');
    }

    /**
     * @inheritDoc
     */
    protected function validate(mixed $v): string
    {
        $v = parent::validate($v);

        if(!Str::isUrl($v, $this->getOption('protocols'))){
            throw new Error($this, 'invalid', ['value' => $v]);
        }

        return $v;
    }
}