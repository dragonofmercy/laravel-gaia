<?php
namespace Gui\Forms\Elements;

use Carbon\Carbon;
use Demeter\Support\Str;
use Gui\Forms\Validators\Error;
use Throwable;

class InputDate extends InputText
{
    public static array $formatOverride = [];

    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addOption('icon', 'far fa-calendar');
        $this->addOption('locale', app()->currentLocale());
        $this->addOption('displayToday', true);
        $this->addOption('withTime', false);
        $this->addOption('timeOnly', false);
        $this->addOption('format', []);
        $this->addOption('startAt');
        $this->addOption('endAt');
        $this->addOption('disabledDates');
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender() : void
    {
        parent::beforeRender();

        $this->setAttribute('autocomplete', 'off');
        $this->setAttribute('inputmode', 'numeric');

        if(!is_array($this->getOption('format'))){
            throw new \InvalidArgumentException("Option [format] must be an array");
        }

        if($this->hasOption('disabledDates') && !is_array($this->getOption('disabledDates'))){
            throw new \InvalidArgumentException("Option [disabledDates] must be an array");
        }

        $this->setOption('format', array_merge(trans('gui::messages.component.datepicker.format'), static::$formatOverride, $this->getOption('format')));
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null) : string
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
            } catch(Throwable) {
            }
        }

        $opt = [];
        $opt['language'] = Str::replace('_', '-', $this->getOption('locale'));
        $opt['trigger'] = '$("#' . $this->generateId($name) . '").next()';
        $opt['timeFormat'] = $format['picker_time_format'];
        $opt['strings'] = [
            'today' => trans('gui::messages.component.datepicker.today'),
            'now' => trans('gui::messages.component.datepicker.now'),
            'clear' => trans('gui::messages.component.datepicker.clear')
        ];

        if($this->hasOption('startAt')){
            $opt['min'] = $this->hasOption('startAt');
        }

        if($this->hasOption('endAt')){
            $opt['max'] = $this->hasOption('endAt');
        }

        if($this->getOption('timeOnly')){
            $opt['timeOnly'] = $this->getOption('timeOnly');
        } else {
            $opt['withTime'] = $this->getOption('withTime');
            $opt['displayToday'] = $this->getOption('displayToday');
            $opt['dateFormat'] = $format['picker_date_format'];

            if($this->hasOption('disabledDates')){
                $opt['disabledDates'] = '["' . implode('","', $this->getOption('disabledDates')) . '"]';
            }
        }

        $input = parent::render($name, $value, $error);
        $button = content_tag('button', gui_icon($this->getOption('icon')), ['class' => Str::join('btn btn-addon inline', $this->isDisabled() ? 'disabled' : ''), 'type' => 'button']);
        $js = '$("#' . $this->generateId($name) . '").GUIControlDatePicker(' . _javascript_php_to_object($opt) . ')';

        return content_tag('div', $input . $button, ['class' => 'input-group']) . javascript_tag_deferred($js);
    }
}