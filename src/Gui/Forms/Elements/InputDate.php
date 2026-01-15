<?php

namespace Gui\Forms\Elements;

use Carbon\Carbon;
use Demeter\Support\Str;
use Gui\Forms\Validators\Error;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Throwable;

class InputDate extends InputGroup
{
    public static array $formatOverride = [];
    public static string $stringPickerToday = "gui::messages.component.datepicker.today";
    public static string $stringPickerNow = "gui::messages.component.datepicker.now";
    public static string $stringPickerClear = "gui::messages.component.datepicker.clear";

    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('icon', '<x-gui::tabler-icon name="calendar-search" />');
        $this->addOption('iconPosition', 'prefix');
        $this->addOption('locale', app()->currentLocale());
        $this->addOption('displayToday', true);
        $this->addOption('withTime', false);
        $this->addOption('timeOnly', false);
        $this->addOption('initialDate');
        $this->addOption('format', []);
        $this->addOption('minutesStep', 5);
        $this->addOption('startAt');
        $this->addOption('endAt');
        $this->addOption('disabledDates');
        $this->addOption('useMask', true);
        $this->addOption('lazyMask', false);
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.input-date';
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        if(!is_array($this->getOption('format'))){
            throw new \InvalidArgumentException("Option [format] must be an array");
        }

        if(!in_array($this->getOption('iconPosition'), ['prefix', 'suffix'])){
            throw new \InvalidArgumentException("Option [iconPrefix] must be prefix or suffix");
        }

        if($this->hasOption('disabledDates') && !is_array($this->getOption('disabledDates'))){
            throw new \InvalidArgumentException("Option [disabledDates] must be an array");
        }

        $this->setAttribute('autocomplete', 'off');
        $this->setAttribute('inputmode', 'numeric');

        $this->setOption($this->getOption('iconPosition'), Blade::render('<a class="link-secondary" data-trigger="pick">' . $this->getOption('icon') . '</a>'));
        $this->setOption('format', array_merge(trans('gui::messages.component.datepicker.format'), static::$formatOverride, $this->getOption('format')));

        $format = $this->getOption('format');

        $componentConfig = [
            'language' => Str::replace('_', '-', $this->getOption('locale')),
            'timeFormat' => $format['picker_time_format'],
            'minutesStep' => $this->getOption('minutesStep'),
            'useMask' => $this->getOption('useMask'),
            'lazyMask' => $this->getOption('lazyMask'),
            'trigger' => ['selector' => '#' . $this->getAttribute('id'), 'chain' => [
                ['method' => 'parent'],
                ['method' => 'find', 'args' => ['[data-trigger=pick]']]
            ]],
            'strings' => [
                'today' => trans(static::$stringPickerToday),
                'now' => trans(static::$stringPickerNow),
                'clear' => trans(static::$stringPickerClear)
            ]
        ];

        if($this->hasOption('initialDate')){
            $componentConfig['initialDate'] = $this->getOption('initialDate');
        }

        if($this->hasOption('startAt')){
            $componentConfig['min'] = $this->getOption('startAt');
        }

        if($this->hasOption('endAt')){
            $componentConfig['max'] = $this->getOption('endAt');
        }

        if($this->getOption('timeOnly')){
            $componentConfig['timeOnly'] = $this->getOption('timeOnly');
        } else {
            $componentConfig['withTime'] = $this->getOption('withTime');
            $componentConfig['displayToday'] = $this->getOption('displayToday');
            $componentConfig['dateFormat'] = $format['picker_date_format'];

            if($this->hasOption('disabledDates')){
                $componentConfig['disabledDates'] = $this->getOption('disabledDates');
            }
        }

        $this->setViewVar('componentConfig', json_encode($componentConfig));
    }

    /**
     * @inheritDoc
     */
    protected function render(string $name, mixed $value = null, ?Error $error = null): string|View
    {
        $format = $this->getOption('format');

        if(!empty($value)){
            if($this->getOption('timeOnly')){
                $displayFormat = $format['time_format'];
            } else {
                $displayFormat = $format['date_format'];
                if($this->getOption('withTime')){
                    $displayFormat = Str::join($displayFormat, $format['time_format']);
                }
            }
            try {
                $value = Carbon::parse($value)->format($displayFormat);
            } catch(Throwable) {}
        }

        return parent::render($name, $value, $error);
    }
}