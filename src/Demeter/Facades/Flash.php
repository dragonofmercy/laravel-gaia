<?php
namespace Demeter\Facades;

use Demeter\User\FlashMessage;
use Demeter\User\FlashMessageType;

/**
 * @method static bool has(string $name)
 * @method static FlashMessage|null get(string $name)
 * @method static void set(string $name, string $message, FlashMessageType $flag = FlashMessageType::SUCCESS, bool $persistant = true)
 */
class Flash extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \Demeter\Support\Flash::class;
    }
}