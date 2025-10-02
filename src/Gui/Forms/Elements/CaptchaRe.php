<?php

namespace Gui\Forms\Elements;

class CaptchaRe extends AbstractCaptchaElement
{
    /**
     * @inheritDoc
     */
    protected function getApiUrl(string $locale): string
    {
        return "https://www.google.com/recaptcha/api.js?hl=$locale";
    }

    /**
     * @inheritDoc
     */
    protected function getSelector(): string
    {
        return "g-recaptcha";
    }
}