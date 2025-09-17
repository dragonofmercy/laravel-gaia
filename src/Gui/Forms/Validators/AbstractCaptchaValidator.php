<?php
namespace Gui\Forms\Validators;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request as RequestFacade;

abstract class AbstractCaptchaValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addRequiredOption('secret');
        $this->setMessage('invalid', 'gui::validation.captcha.invalid');
    }

    /**
     * @inheritDoc
     */
    public function clean(mixed $v): mixed
    {
        return parent::clean(RequestFacade::get($this->getSelector()));
    }

    /**
     * @inheritDoc
     */
    protected function validate(mixed $v): bool
    {
        $response = Http::asForm()->post($this->getApiUrl(), [
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

    /**
     * Retrieves the API URL.
     *
     * @return string The URL of the API.
     */
    abstract protected function getApiUrl(): string;

    /**
     * Retrieves the request selector string.
     *
     * @return string The selector string.
     */
    abstract protected function getSelector(): string;
}