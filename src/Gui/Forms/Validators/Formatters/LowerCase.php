<?php
namespace Gui\Forms\Validators\Formatters;

use Illuminate\Support\Str;
use Gui\Interfaces\ValidatorFormatterInterface;

class LowerCase implements ValidatorFormatterInterface
{
    public function format(mixed $v): string
    {
        return Str::lower((string) $v);
    }
}