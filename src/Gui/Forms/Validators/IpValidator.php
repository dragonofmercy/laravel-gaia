<?php

namespace Gui\Forms\Validators;

class IpValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('flag', FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
    }

    /**
     * @inheritDoc
     * @return string
     */
    protected function validate(mixed $v): string
    {
        if(is_array($v)){
            $v = $this->convertArrayToString($v);
            if($v == '...'){
                $v = '';
            }
        } else {
            $v = strval($v);
        }

        if(!$this->getOption('required') && $this->isEmptyIp($v)){
            return (string) $this->getEmptyValue();
        } elseif($this->getOption('required') && $this->isEmptyIp($v)) {
            throw new Error($this, 'required');
        }

        if(!filter_var($v, FILTER_VALIDATE_IP, $this->getOption('flag'))){
            throw new Error($this, 'invalid', ['value' => $v]);
        }

        return $v;
    }

    /**
     * Check if string is empty ip
     *
     * @param string $value
     * @return bool
     */
    protected function isEmptyIp(string $value): bool
    {
        $test = str_replace('.', '', $value);
        return empty($test);
    }

    /**
     * Convert array to string
     *
     * @param array $haystack
     * @return string
     */
    protected function convertArrayToString(array $haystack): string
    {
        return collect($haystack)->transform(function(mixed $v){
            return null === $v ? "" : $v;
        })->implode('.');
    }
}