<?php
namespace Gui\Forms\Validators\Formatters;

use Illuminate\Support\Str;
use Gui\Interfaces\ValidatorFormatterInterface;

class UpperCase implements ValidatorFormatterInterface
{
    public function format(mixed $v): string
    {
        return Str::upper((string) $v);
    }
}