<?php
namespace Gui\Forms\Validators;

use Illuminate\Support\Facades\Request as RequestFacade;

class ReCaptchaValidator extends HCaptchaValidator
{
    /**
     * Api URL
     * @var string
     */
    protected string $apiUrl = "https://www.google.com/recaptcha/api/siteverify";

    /**
     * @inheritDoc
     */
    protected function getResponse(): string|null
    {
        return RequestFacade::get('g-recaptcha-response');
    }
}