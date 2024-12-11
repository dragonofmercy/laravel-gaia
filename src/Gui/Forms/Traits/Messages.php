<?php
namespace Gui\Forms\Traits;

use Illuminate\Support\Collection;

trait Messages
{
    /**
     * Global default messages
     * @var string[]
     */
    protected static array $defaultMessages = [
        'required' => 'gui::validation.required',
        'invalid' => 'gui::validation.invalid',
    ];

    /**
     * Message collection
     * @var Collection<string, string>
     */
    protected Collection $messages;

    /**
     * Initialize options
     *
     * @return void
     */
    public function initalizeMessages() : void
    {
        $this->messages = collect(self::$defaultMessages);
    }

    /**
     * Validate messages
     *
     * @param Collection|array $messages
     * @return void
     */
    public function validateMessages(Collection|array $messages = []) : void
    {
        collect($messages)->map(function(mixed $value, string $name){
            if(!$this->messages->has($name)){
                throw new \InvalidArgumentException("[" . get_class($this) . "] does not support message [$name]");
            }
        });

        $this->messages = $this->messages->merge($messages);
    }

    /**
     * Set attribute
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setMessage(string $name, string $value) : void
    {
        $this->messages[$name] = $value;
    }

    /**
     * Get attributes
     *
     * @param string $name
     * @return mixed
     */
    public function getMessage(string $name) : string
    {
        return $this->messages->get($name, $name);
    }

    /**
     * Get all messages
     *
     * @return Collection
     */
    public function getMessages() : Collection
    {
        return $this->messages;
    }

    /**
     * Set global default message
     *
     * @param string $name
     * @param string $message
     * @return void
     */
    public static function setDefaultMessage(string $name, string $message) : void
    {
        self::$defaultMessages[$name] = $message;
    }
}