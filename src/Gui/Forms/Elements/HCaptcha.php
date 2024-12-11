<?php
namespace Gui\Forms\Elements;

use Demeter\Support\Str;
use Gui\Forms\Validators\Error;

class HCaptcha extends AbstractElement
{
    /**
     * Api URL
     * @var string
     */
    protected string $apiUrl = "https://hcaptcha.com/1/api.js?hl={locale}&recaptchacompat=off";

    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addRequiredOption('sitekey');
        $this->addOption('locale', app()->currentLocale());
        $this->addOption('theme', 'auto');
        $this->addOption('size', 'normal');
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender() : void
    {
        parent::beforeRender();

        $this->setOption('locale', Str::replace('_', '-', $this->getOption('locale')));

        if($this->getOption('theme') === 'auto'){
            $this->setOption('theme', gui_darkmode() ? 'dark' : 'light');
        }

        $this->appendAttribute('class', 'gui-captcha h-captcha');

        $this->setAttribute('data-sitekey', $this->getOption('sitekey'));
        $this->setAttribute('data-theme', $this->getOption('theme'));
        $this->setAttribute('data-size', $this->getOption('size'));
        $this->setAttribute('data-language', $this->getOption('locale'));
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null) : string
    {
        $this->setAttribute('id', $this->generateId($name));

        $output = $this->renderContentTag('div', attributes: $this->attributes);
        $output.= content_tag('script', attributes: ['src' => Str::replace('{locale}', $this->getOption('locale'), $this->apiUrl), 'async' => null, 'defer' => null]);

        return $output;
    }
}