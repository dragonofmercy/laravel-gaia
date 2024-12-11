<?php
namespace Gui\View\Components;

use Demeter\Support\Str;
use Illuminate\View\Component;

class ThemeSwitcherButton extends Component
{
    public function __construct(
        public bool   $navbar = true,
        public bool   $useIcons = true,
        public array  $icons = ['fas fa-lightbulb', 'far fa-lightbulb'],
        public array  $titles = ['gui::messages.component.theme.dark', 'gui::messages.component.theme.light'],
        public array  $trigger = ['dark', 'light'],
        public string $additionalClasses = '',
    ){}

    public function render() : string
    {
        $index = gui_darkmode() ? 1 : 0;

        if($this->useIcons){
            return content_tag('a', content_tag('i', attributes: ['class' => $this->icons[$index]]), [
                    'href' => '',
                    'data-gui-behavior' => 'theme-switcher',
                    'data-theme' => $this->trigger[$index],
                    'class' => Str::join('gui-theme-switcher', [$this->navbar ? 'navbar-icon' : '', $this->additionalClasses]),
                    'title' => trans($this->titles[$index]),
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'left'
                ]
            );
        } else {
            $uuid = Str::uuid()->toString();
            $attr = ['class' => 'form-check-input', 'type' => 'checkbox', 'id' => $uuid];

            if(gui_darkmode()){
                $attr['checked'] = 'checked';
            }

            $label = content_tag('label', trans('gui::messages.component.theme.dark_mode'), ['class' => 'form-check-label', 'for' => $uuid]);
            return content_tag('div', content_tag('div', tag('input', $attr) . $label, ['class' => 'form-check form-switch', 'data-theme' => $this->trigger[$index], 'data-gui-behavior' => 'theme-switcher']), ['class' => 'gui-theme-switcher']);
        }
    }
}