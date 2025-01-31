<?php
namespace Gui\Forms\Validators;

use Illuminate\Support\Facades\Request as RequestFacade;

class TurnstileCaptchaValidator extends HCaptchaValidator
{
    /**
     * Api URL
     * @var string
     */
    protected string $apiUrl = "https://challenges.cloudflare.com/turnstile/v0/siteverify";

    /**
     * Get Response string from request
     *
     * @return string|null
     */
    protected function getResponse(): string|null
    {
        return RequestFacade::get('cf-turnstile-response');
    }
}