<?php
namespace Gui\Forms\Validators;

class ConcatenateValidator extends StringValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addOption('prefix');
        $this->addOption('suffix');
        $this->addOption('preValidator');
        $this->addOption('postValidator');
    }

    /**
     * @inheritDoc
     */
    protected function validate(mixed $v) : string
    {
        $clean = parent::validate($v);

        if($this->hasOption('preValidator')){
            if($this->hasOption('prefix')){
                $v = $this->cleanUp('prefix', $clean);
            }

            if($this->hasOption('suffix')){
                $v = $this->cleanUp('suffix', $clean);
            }

            $clean = $this->makeValidator('preValidator')->clean($v);
        }

        if($this->hasOption('prefix') && !is_array($this->getOption('prefix'))){
            $clean = $this->getOption('prefix') . $clean;
        }

        if($this->hasOption('suffix') && !is_array($this->getOption('suffix'))){
            $clean.= $this->getOption('suffix');
        }

        if($this->hasOption('postValidator')){
            $clean = $this->makeValidator('postValidator')->clean($v);
        }

        return $clean;
    }

    /**
     * Instantiate validator
     *
     * @param string $type
     * @return AbstractValidator
     */
    protected function makeValidator(string $type) : AbstractValidator
    {
        $validator = $this->getOption($type);

        if(!$validator instanceof AbstractValidator){
            $validator = new $validator();
        }

        return $validator->setOption('required', $this->getOption('required'));
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(mixed $value) : bool
    {
        if(parent::isEmpty($value)){
            return true;
        }

        if($this->hasOption('prefix')){
            $value = $this->cleanUp('prefix', $value);
        }

        if($this->hasOption('suffix')){
            $value = $this->cleanUp('suffix', $value);
        }

        return parent::isEmpty($value);
    }

    /**
     * Clean up value
     *
     * @param string $position
     * @param mixed $value
     * @return string
     */
    protected function cleanUp(string $position, mixed $value) : string
    {
        $option = $this->getOption($position);
        if(is_array($option)){
            foreach($option as $choice){
                if($position === 'prefix'){
                    $pattern = '/^' . preg_quote($choice, '/') . '/';
                } else {
                    $pattern = '/' . preg_quote($choice, '/') . '$/';
                }
                if(preg_match($pattern, $value)){
                    return preg_replace($pattern, '', $value);
                }
            }
            throw new Error($this, 'invalid', ['value' => $value]);
        } else {
            return match ($position){
                'prefix' => preg_replace('/^' . preg_quote($option, '/') . '/', '', $value),
                'suffix' => preg_replace('/' . preg_quote($option, '/') . '$/', '', $value),
            };
        }
    }
}