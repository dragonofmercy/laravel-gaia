<?php
namespace Gui\Forms\Validators;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class MultiFieldsValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addRequiredOption('validators');
        $this->addOption('min');
        $this->addOption('max');
        $this->addOption('removeDuplicates', false);

        $this->setFlag(self::FLAG_SHADOWING);

        $this->setMessage('min', 'gui::validation.line.min');
        $this->setMessage('max', 'gui::validation.line.max');
    }

    /**
     * @inheritDoc
     */
    public function clean(mixed $v) : mixed
    {
        $lines = new Collection();

        if(is_array($v)){
            $fields = array_keys($v);
            $fieldCount = count($v[$fields[0]]);
            for($i = 0; $i < $fieldCount; $i++){
                $item = [];
                foreach($fields as $field) {
                    $item[$field] = $v[$field][$i];
                }
                $lines[] = $item;
            }
        }

        $this->getFormInstance()->setValue($this->getFieldName(), $lines->toArray());

        return parent::clean($lines);
    }

    /**
     * @inheritDoc
     */
    protected function beforeValidation() : void
    {
        parent::beforeValidation();

        $this->setOption('validators', collect($this->getOption('validators')));
    }

    /**
     * @inheritDoc
     */
    protected function validate(mixed $v) : array
    {
        $v = collect($v);
        $errors = [];
        $index = 0;

        $v = $v->transform(function(array $line) use (&$index, &$errors){
            $flagEmpty = true;
            $flagError = false;
            $this->getOption('validators')->map(function(AbstractValidator|string $validator, string $name) use (&$line, $index, &$errors, &$flagEmpty, &$flagError){
                if(!$validator instanceof AbstractValidator){
                    $validator = new $validator(['required' => $this->getOption('required')]);
                }

                $validator->setFormInstance($this->getFormInstance());
                $validator->setFieldName($this->getFieldName());

                try {
                    $line[$name] = $validator->clean($line[$name] ?? null);
                    if($line[$name] !== $validator->getEmptyValue()){
                        $flagEmpty = false;
                    }
                } catch(Error $e) {
                    $flagError = true;
                    Arr::set($errors, $index . '.' . $name, $e->getMessage());
                }
            });

            if($flagEmpty && !$flagError){
                return null;
            }

            $index++;

            return $line;
        })->filter(fn($line) => null !== $line)->values();

        if(count($errors)){
            throw new Error($this, 'invalid', ['errors' => json_encode($errors)]);
        }

        if($this->getOption('removeDuplicates')){
            $v->forget($v->duplicates()->keys());
        }

        if($this->hasOption('min') && $v->count() < $this->getOption('min')){
            throw new Error($this, 'min', ['count' => $v->count(), 'min' => $this->getOption('min')]);
        }

        if($this->hasOption('max') && $v->count() > $this->getOption('max')){
            throw new Error($this, 'max', ['count' => $v->count(), 'max' => $this->getOption('max')]);
        }

        return $v->toArray();
    }

    /**
     * @inheritDoc
     */
    protected function isEmpty(mixed $value) : bool
    {
        return false;
    }
}