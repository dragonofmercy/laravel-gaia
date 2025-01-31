<?php
namespace Gui\Forms\Validators\Formatters;

use Demeter\Support\Str;
use Gui\Contracts\Forms\Validators\Formatter;

class LowerCase implements Formatter
{
    public function format(mixed $v): string
    {
        return Str::lower((string) $v);
    }
}