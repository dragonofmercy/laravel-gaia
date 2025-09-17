<?php
namespace Demeter\Support;

use InvalidArgumentException;
use Gui\View\Components\Flash as FlashComponent;

class Flash
{
    /**
     * Registers a flash message to be displayed in the session.
     *
     * @param string $name The name/key for the flash message entry.
     * @param string $message The content of the flash message.
     * @param string $type The type of the flash message (e.g., success, error). Defaults to `FlashComponent::TYPE_SUCCESS`.
     * @return void
     * @throws InvalidArgumentException If the provided type is not valid.
     */
    public static function registerFlash(string $name, string $message, string $type = FlashComponent::TYPE_SUCCESS): void
    {
        if(!in_array($type, FlashComponent::VALID_TYPES)){
            throw new InvalidArgumentException('Type "' . $type . '" is not valid. Valid types are: ' . implode(', ', FlashComponent::VALID_TYPES));
        }

        session()->flash($name, ['message' => $message, 'type' => $type]);
    }
}