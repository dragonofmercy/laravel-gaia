<?php

namespace Gui\Forms\Validators;

class CaptchaReValidator extends AbstractCaptchaValidator
{
    /**
     * @inheritDoc
     */
    protected function getApiUrl(): string
    {
        return "https://www.google.com/recaptcha/api/siteverify";
    }

    /**
     * @inheritDoc
     */
    protected function getSelector(): string
    {
        return "g-recaptcha-response";
    }
}