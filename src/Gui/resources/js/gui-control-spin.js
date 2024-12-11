!function($){
    'use strict';

    let GUIControlSpin = function(element, options){
        let $this = this;

        this.$element = $(element);
        this.options = $.extend({}, GUIControlSpin.DEFAULTS, options);
        this.$container = this.$element.parent();

        this.direction = 0;
        this.timeoutHandle = null;
        this.intervalHandle = null;

        if(this.options.step.toString().indexOf('.') >= 0){
            this.options.keys.push(110);
            this.options.keys.push(190);
        }

        this.options.min = this.options.min !== null ? parseFloat(this.options.min) : null;
        this.options.max = this.options.max !== null ? parseFloat(this.options.max) : null;

        if((this.isNum(this.options.min) && this.isNum(this.options.max)) && this.options.min > this.options.max){
            console.error('Min option cannot be grather than max option');
        }

        this.options.step = parseFloat(this.options.step);

        this.$btnUp = $('a[data-type=up]', this.$container);
        this.$btnDown = $('a[data-type=down]', this.$container);

        this.$btnUp.on('mousedown touchstart', function(){
            $this.mousedown(1);
        }).on('mouseup mouseout touchend touchcancel', function(){
            $this.stopKeyRepeat();
        });

        this.$btnDown.on('mousedown touchstart', function(){
            $this.mousedown(-1);
        }).on('mouseup mouseout touchend touchcancel', function(){
            $this.stopKeyRepeat();
        });

        this.$element.on('focusout', function(){
            $this.validateValue();
        }).on('keydown', function(e){
            $this.keydown(e);
        }).on('keyup', function(){
            $this.stopKeyRepeat();
        }).on('mousewheel DOMMouseScroll', function(e){
            $this.mouseWheel(e);
        });
    };

    GUIControlSpin.prototype.mouseWheel = function(e){
        this.changeValue((e.originalEvent.wheelDelta > 0 || e.originalEvent.detail < 0) ? 1 : -1);
        e.preventDefault();
    };

    GUIControlSpin.prototype.keydown = function(e){
        if(this.options.keys !== null && $.grep(this.options.keys, function(key){return key === e.keyCode || (key instanceof RegExp && key.test(String.fromCharCode(e.keyCode)));}).length){
            let additionalKeys = { up: 38, down: 40, pageUp: 33, pageDown: 34 };
            this.startKeyRepeat();

            switch(e.keyCode){
                case additionalKeys.up:
                case additionalKeys.pageUp:
                    this.changeValue(1);
                    break;

                case additionalKeys.down:
                case additionalKeys.pageDown:
                    this.changeValue(-1);
                    break;
            }
        } else {
            e.preventDefault();
        }
    };

    GUIControlSpin.prototype.mousedown = function(direction){
        this.startKeyRepeat();
        this.changeValue(direction);
    };

    GUIControlSpin.prototype.changeValue = function(direction){
        let value = this.getValue();

        if(this.$element.not(':disabled').not('[readonly]').length){
            this.direction = direction || 0;

            if(this.direction > 0){
                value += this.options.step;
            } else if(this.direction < 0){
                value -= this.options.step;
            }

            this.validateValue(value);
        }
    };

    GUIControlSpin.prototype.startKeyRepeat = function(){
        let $this = this;
        this.stopKeyRepeat();

        this.timeoutHandle = window.setTimeout(function(){
            $this.changeValue($this.direction);
            $this.intervalHandle = window.setInterval(function(){
                $this.changeValue($this.direction);
            }, $this.options.interval);
        }, this.options.delay);
    };

    GUIControlSpin.prototype.stopKeyRepeat = function(){
        window.clearTimeout(this.timeoutHandle);
        window.clearInterval(this.intervalHandle);
    };

    GUIControlSpin.prototype.validateValue = function(value){
        let precision = 0;
        value = value ?? this.getValue();

        if(value === null){
            return;
        }

        if(this.options.step.toString().indexOf('.') !== false){
            precision = this.options.toString().substring(this.options.step.toString().indexOf('.') + 1).length;
        }

        if(value !== this.options.unlimitedValue){
            if(this.isNum(this.options.max) && value > this.options.max){
                value = this.options.max;
            } else if(this.isNum(this.options.min) && value < this.options.min){
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

        if(value !== parseFloat(this.$element.val())){
            this.$element.val(value.toString());
            this.$element.trigger('gui.spin.change');
        }
    };

    GUIControlSpin.prototype.getValue = function(){
        if(this.isNum(this.$element.val())){
            return parseFloat(this.$element.val());
        } else {
            return this.isNum(this.options.min) ? this.options.min : (this.isNum(this.options.max) && this.options.max < 0 ? this.options.max : null);
        }
    };

    GUIControlSpin.prototype.isNum = function(v){
        return !isNaN(parseFloat(v));
    };

    GUIControlSpin.DEFAULTS = {
        min: 0,
        max: null,
        step: 1,
        unlimitedValue: null,
        keys: [/[0-9]/,9,13,8,46,33,34,37,38,39,40,109,96,97,98,99,100,101,102,103,104,105],
        delay: 500,
        interval: 50,
        iconUp: 'fas fa-angle-up',
        iconDown: 'fas fa-angle-down',
    };

    $.fn.GUIControlSpin = function(option){
        $.each(this, function(){
            let $this   = $(this);
            let data    = $this.data('gui.spin');
            let options = typeof option == 'object' && option;

            if(!data) $this.data('gui.spin', new GUIControlSpin(this, options));
        });
    };

}(jQuery);