<?php

namespace Gui\Forms\Validators\Formatters;

use Gui\Interfaces\ValidatorFormatterInterface;
use Illuminate\Support\Str;

class LowerCase implements ValidatorFormatterInterface
{
    public function format(mixed $v): string
    {
        return Str::lower((string) $v);
    }
}