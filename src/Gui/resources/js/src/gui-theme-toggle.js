import $ from 'jquery';
import { Tooltip } from 'bootstrap';

export class GuiThemeToggle {
    static DATA_STORAGE_KEY = 'dark-mode'

    constructor(element){
        this.$element = $(element);
        this.$input = $(`[type="checkbox"]`, this.$element);
        this.tooltipTitle = this.$element.attr('title').split('|');
        this.tooltip = this._initializeTooltip();

        this.$input.on('change.gui.theme', () => {
            this.toggle();
        });

        this._init();
    }

    toggle(){
        document.cookie = `${GuiThemeToggle.DATA_STORAGE_KEY}=${this._isDarkMode().toString()}; path=/; max-age=${60 * 60 * 24 * 365}; SameSite=Lax`;
        this._updateTheme();
        $(document).trigger('change.gui.theme', this._isDarkMode() ? 'dark' : 'light');
    }

    _init(){
        const storedDarkMode = this._getCookieValue();

        if(storedDarkMode !== null){
            this.$input.prop('checked', storedDarkMode === 'true');
        } else {
            this.$input.prop('checked', window.matchMedia('(prefers-color-scheme: dark)').matches);
        }

        this._updateTheme();
    }

    _initializeTooltip(){
        return new Tooltip(this.$element[0], {
            delay: {show: 50, hide: 50},
            trigger: 'hover',
            animation: false,
            placement: 'bottom'
        });
    }

    _isDarkMode(){
        return this.$input.is(':checked');
    }

    _updateTheme(){
        if(this._isDarkMode()){
            document.body.setAttribute('data-bs-theme', 'dark');
            this.tooltip.setContent({'.tooltip-inner': this.tooltipTitle[1]});
        } else {
            document.body.removeAttribute('data-bs-theme');
            this.tooltip.setContent({'.tooltip-inner': this.tooltipTitle[0]});
        }
    }

    _getCookieValue(){
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${GuiThemeToggle.DATA_STORAGE_KEY}=`);
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
        return null;
    }
}

$.fn.GuiThemeToggle = function(){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.theme-toggle');
        if(!data) $this.data('gui.theme-toggle', new GuiThemeToggle(this));
    });
};

export default GuiThemeToggle;