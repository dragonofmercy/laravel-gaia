<?php
namespace Gui\Forms\Elements;

class CaptchaTurnstile extends AbstractCaptchaElement
{
    /**
     * @inheritDoc
     */
    protected function getApiUrl(string $locale): string
    {
        return "https://challenges.cloudflare.com/turnstile/v0/api.js";
    }

    /**
     * @inheritDoc
     */
    protected function getSelector(): string
    {
        return "cf-turnstile";
    }
}