<?php

namespace Gui\Forms\Elements;

class CaptchaH extends AbstractCaptchaElement
{
    /**
     * @inheritDoc
     */
    protected function getApiUrl(string $locale): string
    {
        return "https://hcaptcha.com/1/api.js?hl=$locale&recaptchacompat=off";
    }

    /**
     * @inheritDoc
     */
    protected function getSelector(): string
    {
        return "h-captcha";
    }
}