<?php
namespace Gui\Forms\Validators;

class CaptchaTurnstileValidator extends AbstractCaptchaValidator
{
    /**
     * @inheritDoc
     */
    protected function getApiUrl(): string
    {
        return "https://challenges.cloudflare.com/turnstile/v0/siteverify";
    }

    /**
     * @inheritDoc
     */
    protected function getSelector(): string
    {
        return "cf-turnstile-response";
    }
}