<?php
namespace Gui\Forms\Elements;

use Demeter\Support\Str;

class ReCaptcha extends HCaptcha
{
    /**
     * Api URL
     * @var string
     */
    protected string $apiUrl = "https://www.google.com/recaptcha/api.js?hl={locale}";

    /**
     * @inheritDoc
     */
    protected function beforeRender() : void
    {
        parent::beforeRender();

        $this->setAttribute('class', Str::replace('h-captcha', 'g-recaptcha', $this->getAttribute('class')));
    }
}