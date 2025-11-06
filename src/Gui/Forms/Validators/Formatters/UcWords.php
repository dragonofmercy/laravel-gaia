<?php

namespace Gui\Forms\Validators\Formatters;

use Gui\Interfaces\ValidatorFormatterInterface;
use Illuminate\Support\Str;

class UcWords implements ValidatorFormatterInterface
{
    public function format(mixed $v): string
    {
        return Str::title((string) $v);
    }
}