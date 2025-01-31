<?php
namespace Gui\Forms\Elements;

use Demeter\Support\Str;

class TurnstileCaptcha extends HCaptcha
{
    /**
     * Api URL
     * @var string
     */
    protected string $apiUrl = "https://challenges.cloudflare.com/turnstile/v0/api.js";

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->setAttribute('class', Str::replace('h-captcha', 'cf-turnstile', $this->getAttribute('class')));
    }
}