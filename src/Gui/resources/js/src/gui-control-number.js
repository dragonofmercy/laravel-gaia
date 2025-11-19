import $ from 'jquery';
import { isCtrlPressed, Keycodes } from './lib/keycodes.js';

export class GuiControlNumber {

    static DEFAULTS = {
        min: null,
        max: null,
        step: 1,
        unlimitedValue: null,
        delay: 500,
        interval: 50,
        keys: [8,9,13,16,17,18,33,34,35,36,37,38,39,40,46,48,49,50,51,52,53,54,55,56,57,96,97,98,99,100,101,102,103,104,105,109,189]
    }

    constructor(element, options){
        this.options = $.extend({}, GuiControlNumber.DEFAULTS, options);
        this.$element = $(element);

        this._timeoutHandle = null;
        this._intervalHandle = null;

        this.options.unlimitedValue = this.options.unlimitedValue !== null ? parseFloat(this.options.unlimitedValue) : null;
        this.options.min = this.options.min !== null ? parseFloat(this.options.min) : null;
        this.options.max = this.options.max !== null ? parseFloat(this.options.max) : null;
        this.options.step = parseFloat(this.options.step);

        if(this.options.step.toString().indexOf('.') >= 0){
            this.options.keys.push(110);
            this.options.keys.push(190);
        }

        if((this._isNum(this.options.min) && this._isNum(this.options.max)) && this.options.min > this.options.max){
            console.error('Min option cannot be grather than max option');
        }

        this.$btns = this.$element.parent().find('[data-trigger]');

        this.$btns.on('mousedown touchstart', e => {
            e.preventDefault();
            this._mouseDown(e);
        }).on('mouseup mouseout touchend touchcancel', () => {
            this._stopTimer();
        });

        this.$element.on('focusout', () => {
            this.validateValue();
        }).on('keydown', e => {
            this._keyDown(e);
        }).on('keyup', () => {
            this._stopTimer();
        }).on('paste', () => {
            setTimeout(() => {
                this.validateValue();
            }, 50);
        });
    }

    changeValue(up){
        let value = this.getValue();
        value += up ? this.options.step : -this.options.step;
        this.validateValue(value);
    }

    getValue(){
        if(this._isNum(this.$element.val())){
            return parseFloat(this.$element.val());
        } else {
            return this._isNum(this.options.min) ? this.options.min : (this._isNum(this.options.max) && this.options.max < 0 ? this.options.max : null);
        }
    }

    validateValue(value){
        let precision = 0;
        value = value ?? this.getValue();

        if(value === null){
            return;
        }

        if(this.options.step.toString().indexOf('.') !== false){
            precision = this.options.toString().substring(this.options.step.toString().indexOf('.') + 1).length;
        }

        if(value !== this.options.unlimitedValue){
            if(this._isNum(this.options.max) && value > this.options.max){
                value = this.options.max;
            } else if(this._isNum(this.options.min) && value < this.options.min){
                if(this.options.unlimitedValue !== null && this.options.unlimitedValue < this.options.min){
                    if(value == this.options.unlimitedValue + this.options.step){
                        value = this.options.min;
                    } else {
                        value = this.options.unlimitedValue;
                    }
                } else {
                    value = this.options.min;
                }
            }
        }

        value = Number(value.toFixed(precision));

        if(value !== this.$element.val()){
            this.$element.val(value.toString()).trigger('change.gui');
        }
    }

    _isNum(v){
        return !isNaN(parseFloat(v));
    }

    _mouseDown(e){
        const $pressedButton = $(e.currentTarget);
        this._startTimer($pressedButton);
        this.changeValue($pressedButton.data('trigger') == 'up');
    }

    _keyDown(e){
        if(!this.options.keys.includes(e.keyCode) && !isCtrlPressed()){
            e.preventDefault();
            return;
        }

        switch(e.keyCode){
            case Keycodes.PAGE_UP:
                e.preventDefault();
                this.changeValue(true);
                break;

            case Keycodes.PAGE_DOWN:
                e.preventDefault();
                this.changeValue(false)
                break;
        }
    }

    _startTimer($pressedButton){
        this._timeoutHandle = window.setTimeout(() => {
            this.changeValue($pressedButton.data('trigger') == 'up');
            this._intervalHandle = window.setInterval(() => {
                this.changeValue($pressedButton.data('trigger') == 'up');
            }, this.options.interval);
        }, this.options.delay);
    }

    _stopTimer(){
        window.clearTimeout(this._timeoutHandle);
        window.clearInterval(this._intervalHandle);
    }
}

$.fn.GuiControlNumber = function(option){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.control');
        const options = typeof option == 'object' && option;
        if(!data) $this.data('gui.control', new GuiControlNumber(this, options));
    });
};

export default GuiControlNumber;