<?php
namespace Gui\Forms\Validators;

use Gui\Forms\Traits\FieldName;
use Gui\Forms\Traits\Flags;
use Gui\Forms\Traits\Messages;
use Gui\Forms\Traits\Options;
use Gui\Forms\Traits\Form;
use Illuminate\Support\Collection;

abstract class AbstractValidator
{
    use Options, Messages, Flags, Form, FieldName;

    const FLAG_GLOBAL = 'global';
    const FLAG_FIELD = 'field';
    const FLAG_ABORT = 'abort';
    const FLAG_SHADOWING = 'shadowing';

    /**
     * Constructor.
     *
     * @param Collection|array $options
     * @param Collection|array $messages
     * @param Collection|array $flags
     */
    public function __construct(Collection|array $options = [], Collection|array $messages = [], Collection|array $flags = [])
    {
        $defaultOptions = [
            'required' => true,
            'trim' => false,
            'emptyValue' => null,
            'formatter' => null
        ];

        $this->initalizeOptions($defaultOptions);
        $this->initalizeMessages();
        $this->initalizeFlags($flags);

        $this->initialize();

        $this->validateOptions($options);
        $this->validateMessages($messages);

        $this->beforeValidation();
    }

    /**
     * Clean value
     *
     * @param mixed $v
     * @return mixed
     */
    public function clean(mixed $v): mixed
    {
        if(is_string($v) && $this->options->get('trim', false)){
            $v = trim($v);
        }

        if($this->isEmpty($v)){
            if($this->getOption('required')){
                throw new Error($this, 'required');
            }

            return $this->getEmptyValue();
        }

        $v = $this->validate($v);

        if($this->hasOption('formatter')){
            $formatterClass = $this->options->get('formatter');
            $v = (new $formatterClass)->format($v);
        }

        return $v;
    }

    /**
     * Function called just before validation, options passed with
     * constructor are accessible at this level
     *
     * @return void
     */
    protected function beforeValidation(): void
    {
    }

    /**
     * Initialize element
     *
     * @return void
     */
    protected function initialize(): void
    {
    }

    /**
     * Return if the value is empty
     *
     * @param mixed $value
     * @return bool
     */
    protected function isEmpty(mixed $value): bool
    {
        return in_array($value, [null, '', array()], true);
    }

    /**
     * Get empty value
     *
     * @return mixed
     */
    protected function getEmptyValue(): mixed
    {
        return $this->getOption('emptyValue');
    }

    /**
     * Validate value and return validated value
     *
     * @param mixed $v
     * @return mixed
     */
    abstract protected function validate(mixed $v): mixed;
}