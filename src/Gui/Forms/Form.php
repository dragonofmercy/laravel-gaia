<?php
namespace Gui\Forms;

use Gui\Forms\Decorators\AbstractDecorator;
use Gui\Forms\Decorators\DefaultDecorator;
use Gui\Forms\Elements\AbstractElement;
use Gui\Forms\Validators\Error;
use Gui\Forms\Validators\AbstractValidator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Str;

abstract class Form implements Htmlable
{
    const GLOBAL_ERROR_FLAG = 'gui_global_error';

    /**
     * Default decorator classname
     * @var string
     */
    public static string $defaultDecorator = DefaultDecorator::class;

    /**
     * @var Collection<string, mixed>
     */
    protected Collection $defaults;

    /**
     * @var Collection<string, AbstractElement>
     */
    protected Collection $elements;

    /**
     * @var Collection<string, AbstractValidator>
     */
    protected Collection $validators;

    /**
     * @var Collection<AbstractValidator>
     */
    protected Collection $globalValidators;

    /**
     * @var Collection<string, string>
     */
    protected Collection $labels;

    /**
     * @var Collection<string, string>
     */
    protected Collection $helps;

    /**
     * @var Collection<string, string>
     */
    protected Collection $separators;

    /**
     * @var Collection<string, mixed>
     */
    protected Collection $values;

    /**
     * @var Collection<string, Error>
     */
    protected Collection $errors;

    /**
     * Form name
     * @var string
     */
    protected string $name;

    /**
     * Decorator name
     * @var string|null
     */
    protected ?string $decorator = null;

    /**
     * Decorator instance
     *
     * @var AbstractDecorator|null
     */
    protected ?AbstractDecorator $decoratorInstance = null;

    /**
     * Flag if form has been binded
     * @var bool
     */
    protected bool $isBound = false;

    /**
     * Constructor.
     *
     * @param array $defaults
     */
    public function __construct(array $defaults = [])
    {
        $this->elements = new Collection();
        $this->validators = new Collection();
        $this->globalValidators = new Collection();
        $this->labels = new Collection();
        $this->helps = new Collection();
        $this->separators = new Collection();
        $this->values = new Collection();
        $this->errors = new Collection();
        $this->defaults = collect($defaults);

        $this->setName((new \ReflectionClass($this))->getShortName());
        $this->configure();
    }

    /**
     * Set decorator
     *
     * @param string $decorator
     * @return void
     */
    public function setDecorator(string $decorator): void
    {
        $this->decorator = $decorator;
    }

    /**
     * Bind form
     *
     * @param array $values
     * @param array $files
     * @return bool
     */
    public function bind(array $values = [], array $files = []): bool
    {
        $values = collect(!empty($values) ? $values : RequestFacade::post($this->name))->merge(!empty($files) ? $files : RequestFacade::file($this->name));

        $abort = false;
        $validators = $this->getValidators();

        $this->isBound = true;

        foreach($validators as $name => $validator){
            $value = $values[$name] ?? null;
            if(null !== $this->getElement($name)){
                if($abort === false){
                    try {
                        $this->setValue($name, $validator->clean($value));
                    } catch(Error $e) {
                        if($this->getElement($name)->isHidden()){
                            $e->getValidator()->setFlag(AbstractValidator::FLAG_GLOBAL);
                        }

                        $this->errors[$name] = $e;

                        if(!$validator->getFlag(AbstractValidator::FLAG_SHADOWING)){
                            $this->setValue($name, $value);
                        }

                        if($validator->getFlag(AbstractValidator::FLAG_ABORT) === true){
                            $abort = true;
                        }
                    }
                }
            }
        }

        if($abort === false){
            foreach($this->globalValidators as $validator){
                try {
                    $this->values = $validator->clean($this->values);
                } catch(Error $e) {
                    if(null !== $validator->getFlag(AbstractValidator::FLAG_FIELD)){
                        $this->errors[$validator->getFlag(AbstractValidator::FLAG_FIELD)] = $e;
                    } else {
                        $e->getValidator()->setFlag(AbstractValidator::FLAG_GLOBAL);
                        $this->errors[] = $e;
                    }
                }
            }
        }

        return $this->isValid();
    }

    /**
     * Get if form is valid
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isBound && $this->errors->count() === 0;
    }

    /**
     * Get form name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set form name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = Str::lower($name);
    }

    /**
     * Render form
     *
     * @return mixed
     */
    public function __toString(): string
    {
        return $this->getDecorator()->render();
    }

    /**
     * Get content as a string of HTML.
     * This allow the usage of {{ $form }} instead of {!! $form !!} in blade views
     *
     * @return mixed|string
     */
    public function toHtml()
    {
        return $this->__toString();
    }

    /**
     * Get form decorator
     *
     * @return AbstractDecorator
     */
    public function getDecorator(): AbstractDecorator
    {
        if(null !== $this->decoratorInstance){
            return $this->decoratorInstance;
        }

        if(null === $this->decorator){
            $this->decorator = static::$defaultDecorator;
        }

        $this->decoratorInstance = new $this->decorator($this);

        return $this->decoratorInstance;
    }

    /**
     * Get global error collection
     *
     * @return Collection<string, Error>
     */
    public function getGlobalErrors(): Collection
    {
        $global = new Collection();

        $this->errors->map(function(Error $e, string $name) use (&$global){
            if($e->getValidator()->getFlag('global')){
                $global[$name] = $e;
            }
        });

        return $global;
    }

    /**
     * Get error object
     *
     * @param string $name
     * @return Error|null
     */
    public function getError(string $name): Error|null
    {
        return $this->errors->get($name);
    }

    /**
     * Get all errors
     *
     * @return Collection<string, Error>
     */
    public function getErrors(): Collection
    {
        return $this->errors;
    }

    /**
     * Set labels
     *
     * @param array<string, string|bool> $labels
     * @return void
     */
    public function setLabels(array $labels): void
    {
        foreach($labels as $name => $label){
            $this->setLabel($name, $label);
        }
    }

    /**
     * Set label
     *
     * @param string $name
     * @param string|bool $label
     * @return void
     */
    public function setLabel(string $name, string|bool $label): void
    {
        $this->labels[$name] = $label;
    }

    /**
     * Get label
     *
     * @param string $name
     * @return string|bool
     */
    public function getLabel(string $name): string|bool
    {
        return trans($this->labels->get($name, $name));
    }

    /**
     * Get labels
     *
     * @return Collection<string, string|bool>
     */
    public function getLabels(): Collection
    {
        return $this->labels;
    }
    
    /**
     * Set values
     *
     * @param array $values
     * @return void
     */
    public function setValues(array $values): void
    {
        foreach($values as $name => $value){
            $this->setValue($name, $value);
        }
    }

    /**
     * Set value
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setValue(string $name, mixed $value): void
    {
        $this->values[$name] = $value;
    }

    /**
     * Get values
     *
     * @return Collection
     */
    public function getValues(): Collection
    {
        return $this->elements->map(function(mixed $value, string $name){
            return $this->getValue($name);
        });
    }

    /**
     * Get value
     *
     * @param string $name
     * @return mixed
     */
    public function getValue(string $name): mixed
    {
        return $this->values->get($name, $this->defaults->get($name));
    }

    /**
     * Set global validator
     *
     * @param AbstractValidator $validator
     * @return void
     */
    public function setGlobalValidator(AbstractValidator $validator): void
    {
        $validator->setFormInstance($this);

        $this->globalValidators[] = $validator;
    }

    /**
     * Set separator
     *
     * @param string $before
     * @param string $title
     * @param string|null $help
     * @return void
     */
    public function setSeparator(string $before, string $title, string|null $help = null): void
    {
        $separator = new Collection([
            'title' => $title,
            'help' => $help
        ]);

        $this->separators[$before] = $separator;
    }

    /**
     * Get separator
     *
     * @param string $before
     * @return Collection|null
     */
    public function getSeparator(string $before): Collection|null
    {
        return $this->separators->get($before);
    }

    /**
     * Set validators
     *
     * @param array<string, AbstractValidator> $validators
     * @return void
     */
    public function setValidators(array $validators): void
    {
        foreach($validators as $name => $validator){
            $this->setValidator($name, $validator);
        }
    }

    /**
     * Set validator
     *
     * @param string $name
     * @param AbstractValidator $validator
     * @return void
     */
    public function setValidator(string $name, AbstractValidator $validator): void
    {
        $validator->setFormInstance($this);
        $validator->setFieldName($name);

        $this->validators[$name] = $validator;
    }

    /**
     * Get validator
     *
     * @param string $name
     * @return AbstractValidator|null
     */
    public function getValidator(string $name): AbstractValidator|null
    {
        return $this->validators->get($name);
    }

    /**
     * Get validators
     *
     * @return Collection<string, AbstractValidator>
     */
    public function getValidators(): Collection
    {
        return $this->validators;
    }

    /**
     * Set helps
     *
     * @param array<string, string> $helps
     * @return void
     */
    public function setHelps(array $helps): void
    {
        foreach($helps as $name => $help){
            $this->setHelp($name, $help);
        }
    }

    /**
     * Set help
     *
     * @param string $name
     * @param string $help
     * @return void
     */
    public function setHelp(string $name, string $help): void
    {
        $this->helps[$name] = $help;
    }

    /**
     * Get help
     *
     * @param string $name
     * @return string|null
     */
    public function getHelp(string $name): string|null
    {
        return $this->helps->get($name);
    }

    /**
     * Get helps
     *
     * @return Collection<string, string>
     */
    public function getHelps(): Collection
    {
        return $this->helps;
    }

    /**
     * Set defaults
     *
     * @param array<string, mixed> $defaults
     * @return void
     */
    public function setDefaults(array $defaults): void
    {
        foreach($defaults as $name => $default){
            $this->setDefault($name, $default);
        }
    }

    /**
     * Set default
     *
     * @param string $name
     * @param mixed $default
     * @return void
     */
    public function setDefault(string $name, mixed $default): void
    {
        $this->defaults[$name] = $default;
    }

    /**
     * Get default
     *
     * @param string $name
     * @return string|null
     */
    public function getDefault(string $name): string|null
    {
        return $this->defaults->get($name);
    }

    /**
     * Get defaults
     *
     * @return Collection<string, mixed>
     */
    public function getDefaults(): Collection
    {
        return $this->defaults;
    }

    /**
     * Set elements
     *
     * @param array<string, AbstractElement> $elements
     * @return void
     */
    public function setElements(array $elements): void
    {
        foreach($elements as $name => $element){
            $this->setElement($name, $element);
        }
    }

    /**
     * Set element
     *
     * @param string $name
     * @param mixed $element
     * @return void
     */
    public function setElement(string $name, AbstractElement $element): void
    {
        $element->setFormInstance($this);
        $element->setFieldName($name);

        $this->elements[$name] = $element;
    }

    /**
     * Get element
     *
     * @param string $name
     * @return AbstractElement|null
     */
    public function getElement(string $name): AbstractElement|null
    {
        return $this->elements->get($name);
    }

    /**
     * Get elements
     *
     * @return Collection<string, AbstractElement>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    abstract protected function configure(): void;
}