<?php
namespace Gui\Support;

class Gui
{
    /**
     * The name of the cookie used to store the dark mode preference.
     */
    const DARK_MODE_COOKIE_NAME = 'dark-mode';

    /**
     * Determines if dark mode is currently enabled based on the stored cookie value.
     *
     * @return bool Returns true if dark mode is enabled, false otherwise.
     */
    public static function isDarkMode(): bool
    {
        return \Illuminate\Support\Facades\Cookie::get(self::DARK_MODE_COOKIE_NAME) === 'true';
    }
}