<?php
namespace Gui\Forms\Validators\Formatters;

use Demeter\Support\Str;
use Gui\Contracts\Forms\Validators\Formatter;

class UcWords implements Formatter
{
    public function format(mixed $v) : string
    {
        return Str::title((string) $v);
    }
}