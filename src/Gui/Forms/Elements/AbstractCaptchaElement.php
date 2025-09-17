<?php
namespace Gui\Forms\Elements;

use Illuminate\Support\Str;

abstract class AbstractCaptchaElement extends AbstractElement
{
    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.captcha';
    }

    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addRequiredOption('sitekey');
        $this->addOption('locale', app()->currentLocale());
        $this->addOption('theme', 'auto');
        $this->addOption('size', 'normal');

        $this->size = 12;
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->setOption('locale', Str::replace('_', '-', $this->getOption('locale')));

        $this->appendAttribute('class', 'gui-captcha ' . $this->getSelector());

        $this->setAttribute('data-sitekey', $this->getOption('sitekey'));
        $this->setAttribute('data-theme', $this->getOption('theme'));
        $this->setAttribute('data-size', $this->getOption('size'));
        $this->setAttribute('data-language', $this->getOption('locale'));

        $this->setViewVar('src', $this->getApiUrl($this->getOption('locale')));
    }

    /**
     * Retrieves the API URL for a given locale.
     *
     * @param string $locale The locale for which the API URL should be generated.
     * @return string The API URL corresponding to the specified locale.
     */
    abstract protected function getApiUrl(string $locale): string;

    /**
     * Retrieves the captcha selector.
     *
     * @return string The selector value.
     */
    abstract protected function getSelector(): string;
}