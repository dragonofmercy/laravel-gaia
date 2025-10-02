<?php

namespace Gui\Interfaces;

interface ValidatorFormatterInterface
{
    /**
     * Formats the given value.
     *
     * @param mixed $v The value to be formatted.
     * @return mixed The formatted value.
     */
    public function format(mixed $v): mixed;
}