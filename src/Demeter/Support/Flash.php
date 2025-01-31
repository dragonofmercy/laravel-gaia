<?php
namespace Demeter\Support;

use Demeter\User\FlashMessage as UserFlash;
use Demeter\User\FlashMessageType;
use Illuminate\Support\Facades\Session as SessionFacade;

class Flash
{
    const STORE_PATH = "gui.flash";

    /**
     * Set flash message
     *
     * @param string $name
     * @param string $message
     * @param FlashMessageType $flag
     * @param bool $persistant
     * @return void
     */
    public function set(string $name, string $message, FlashMessageType $flag = FlashMessageType::SUCCESS, bool $persistant = true): void
    {
        $toStore = ['message' => $message, 'flag' => $flag->value];
        $method = $persistant ? 'flash' : 'now';

        SessionFacade::{$method}($this->getStorePath($name), $toStore);
    }

    /**
     * Get flash if exists
     *
     * @param string $name
     * @return UserFlash|null
     */
    public function get(string $name): UserFlash|null
    {
        if($this->has($name)){
            return UserFlash::fromArray(SessionFacade::get($this->getStorePath($name)));
        }

        return null;
    }

    /**
     * Get if flash exists
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return SessionFacade::has($this->getStorePath($name));
    }

    /**
     * Get store path
     *
     * @param string $name
     * @return string
     */
    protected function getStorePath(string $name): string
    {
        return self::STORE_PATH . '.' . $name;
    }
}