<?php

namespace Gui\Forms\Validators;

class CaptchaHValidator extends AbstractCaptchaValidator
{
    /**
     * @inheritDoc
     */
    protected function getApiUrl(): string
    {
        return "https://hcaptcha.com/siteverify";
    }

    /**
     * @inheritDoc
     */
    protected function getSelector(): string
    {
        return "h-captcha-response";
    }
}