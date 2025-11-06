<?php

namespace Demeter\Support;

use Gui\View\Components\Flash as FlashComponent;
use InvalidArgumentException;

class Flash
{
    /**
     * Registers a flash message to be stored in the session.
     *
     * @param string $name The name or key under which the flash message will be stored.
     * @param string $message The content of the flash message.
     * @param string $type The type of the flash message (e.g., success, error, etc.). Defaults to FlashComponent::TYPE_SUCCESS.
     * @param bool $persistant Determines whether the flash message should persist across multiple requests. Defaults to true.
     * @throws InvalidArgumentException If an invalid type is provided that is not in FlashComponent::VALID_TYPES.
     */
    public static function registerFlash(string $name, string $message, string $type = FlashComponent::TYPE_SUCCESS, bool $persistant = true): void
    {
        if(!in_array($type, FlashComponent::VALID_TYPES)){
            throw new InvalidArgumentException('Type "' . $type . '" is not valid. Valid types are: ' . implode(', ', FlashComponent::VALID_TYPES));
        }

        $method = $persistant ? 'flash' : 'now';
        session()->$method($name, ['message' => $message, 'type' => $type]);
    }
}