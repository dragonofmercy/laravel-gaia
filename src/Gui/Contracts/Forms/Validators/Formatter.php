<?php
namespace Gui\Contracts\Forms\Validators;

interface Formatter
{
    public function format(mixed $v) : mixed;
}