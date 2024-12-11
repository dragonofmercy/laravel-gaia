!function($){
    'use strict';

    let GUIControlRange = function(element, options){
        let $this = this;

        this.$element = $(element);
        this.options = $.extend({}, GUIControlRange.DEFAULTS, $.extend({}, this.$element.data(), options));
        this.popupTimeout = null;

        if(!this.$element.parent().hasClass('gui-input-range')){
            this.$element.wrap('<div class="gui-input-range" />');
        }

        if(this.options.rangeTooltip){
            this.$tip = $('<div class="range-tooltip tooltip bs-tooltip-top"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>');
            this.$tip.insertAfter(this.$element);
        }

        if(this.options.rangeProgression){
            this.$element.addClass('show-progress');
            this.$element.parent().append('<div class="range-progress" />');
            this.$element.parent().append('<div class="range-track" />');
        }

        this.$list = $('#' + this.$element.attr('list'));

        if(this.$element.attr('disabled') || this.$element.attr('readonly')){
            this.$element.parent().addClass('disabled');
            this.$element.on('focus', function(){
                $this.$element.trigger('blur');
            });
        }

        this.$element.on('input', function(){
            $this.update();
        }).on('keydown', function(e){
            if(e.keyCode >= 37 && e.keyCode <= 40){
                $this.$element.addClass('active');
                if($this.popupTimeout !== null){
                    clearTimeout($this.popupTimeout);
                }
                $this.popupTimeout = setTimeout(function(){
                    $this.$element.removeClass('active');
                }, 1000)
            }
        });

        this.$element.trigger('input');
    };

    GUIControlRange.prototype.update = function(){
        let val = this.$element.val();
        let min = this.$element.attr('min') ? this.$element.attr('min') : 0;
        let max = this.$element.attr('max') ? this.$element.attr('max') : 100;
        let pos = Number(((val - min) * 100) / (max - min));

        this.$element.trigger('gui.range.input', [pos]);

        if(this.options.rangeProgression){
            $('.range-progress', this.$element.parent()).css('width', `calc(${pos}% + (${8 - pos * 0.15}px))`);
        }

        if(this.options.rangeTooltip){
            if(this.$list.length && this.$list.find('option[value=' + val + ']').length){
                $('.tooltip-inner', this.$tip).html(this.$list.find('option[value=' + val + ']').attr('label'));
            } else {
                $('.tooltip-inner', this.$tip).html(this.format(val.toString()));
            }

            let top = `calc(${this.$tip.outerHeight()}px * -1)`;
            let leftMargin = this.$tip.outerWidth() * -.5;

            this.$tip.css({
                left: `calc(${pos}% + (${8 - pos * 0.15}px))`,
                marginLeft: leftMargin,
                top: top
            });
        }
    };

    GUIControlRange.prototype.format = function(...args){
        return this.options.rangeTooltipFormat.replace(/(\{\d+\})/g, function(a) {
            return args[+(a.substring(1, a.length - 2)) || 0];
        });
    };

    GUIControlRange.DEFAULTS = {
        rangeProgression: true,
        rangeTooltip: true,
        rangeTooltipFormat: "{0}"
    };

    $.fn.GUIControlRange = function(option){
        $.each(this, function(){
            let $this = $(this);
            let data = $this.data('gui.range');
            let options = typeof option == 'object' && option;
            if(!data) $this.data('gui.range', new GUIControlRange(this, options));
        });
    };

}(jQuery);