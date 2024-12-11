<?php
namespace Gui\Forms\Validators;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request as RequestFacade;

class HCaptchaValidator extends AbstractValidator
{
    /**
     * Api URL
     * @var string
     */
    protected string $apiUrl = "https://hcaptcha.com/siteverify";

    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addRequiredOption('secret');

        $this->setMessage('invalid', 'gui::validation.captcha.invalid');
    }

    /**
     * @inheritDoc
     */
    public function clean(mixed $v) : mixed
    {
        return parent::clean($this->getResponse());
    }

    /**
     * Get Response string from request
     *
     * @return string|null
     */
    protected function getResponse() : string|null
    {
        return RequestFacade::get('h-captcha-response');
    }

    /**
     * @inheritDoc
     */
    protected function validate(mixed $v) : bool
    {
        $response = Http::asForm()->post($this->apiUrl, [
            'secret' => $this->getOption('secret'),
            'response' => $v
        ]);

        if($response->ok()){
            $body = collect(json_decode($response->body(), true));

            if($body->get('success')){
                return true;
            }
        }

        throw new Error($this, 'invalid');
    }
}