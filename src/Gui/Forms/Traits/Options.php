<?php
namespace Gui\Forms\Traits;

use Gui\Forms\Elements\AbstractElement;
use Gui\Forms\Validators\AbstractValidator;
use Illuminate\Support\Collection;

trait Options
{
    /**
     * Options definitions collection
     * @var Collection<string, bool>
     */
    protected Collection $optionsDefinitions;

    /**
     * Options collection
     * @var Collection<string, mixed>
     */
    protected Collection $options;

    /**
     * Initialize options
     *
     * @param Collection|array $defaultOptions
     * @return void
     */
    public function initalizeOptions(Collection|array $defaultOptions = []): void
    {
        $this->optionsDefinitions = new Collection();
        $this->options = collect($defaultOptions)->map(function(mixed $value, string $key){
            $this->optionsDefinitions[$key] = false;
            return $value;
        });
    }

    /**
     * Validate options
     *
     * @param Collection|array $options
     * @return void
     */
    public function validateOptions(Collection|array $options = []): void
    {
        $this->options = $this->options->merge($options);
        $this->optionsDefinitions->map(function(bool $required, string $name){
            if($required && null === $this->options->get($name)){
                throw new \InvalidArgumentException("[" . get_class($this) . "] require option [$name]");
            }
        });

        $this->options->map(function(mixed $value, string $name){
            if(!$this->optionsDefinitions->has($name)){
                throw new \InvalidArgumentException("[" . get_class($this) . "] does not support option [$name]");
            }
        });
    }

    /**
     * Add required option
     *
     * @param string $name
     * @return AbstractElement|Options|AbstractValidator
     */
    public function addRequiredOption(string $name): self
    {
        $this->optionsDefinitions[$name] = true;

        return $this;
    }

    /**
     * Add optional option with it's default value
     *
     * @param string $name
     * @param mixed|null $default
     * @return AbstractElement|Options|AbstractValidator
     */
    public function addOption(string $name, mixed $default = null): self
    {
        $this->optionsDefinitions[$name] = false;

        if(null === $this->options->get($name)){
            $this->options[$name] = $default;
        }

        return $this;
    }

    /**
     * Set option value
     *
     * @param string $name
     * @param mixed $value
     * @return AbstractElement|Options|AbstractValidator
     */
    public function setOption(string $name, mixed $value): self
    {
        if(!$this->optionsDefinitions->has($name)){
            throw new \InvalidArgumentException("[" . get_class($this) . "] does not support option [$name]");
        }

        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Remove option
     *
     * @param string $name
     * @return AbstractElement|Options|AbstractValidator
     */
    public function removeOptions(string $name): self
    {
        $this->optionsDefinitions->forget($name);
        $this->options->forget($name);

        return $this;
    }

    /**
     * Get option value
     *
     * @param string $name
     * @return mixed
     */
    public function getOption(string $name): mixed
    {
        if(!$this->optionsDefinitions->has($name)){
            throw new \InvalidArgumentException("[" . get_class($this) . "] does not support option [$name]");
        }

        return $this->options->get($name);
    }

    /**
     * Check if option exists
     *
     * @param string $name
     * @return bool
     */
    public function hasOption(string $name): bool
    {
        return null !== $this->options->get($name);
    }
}