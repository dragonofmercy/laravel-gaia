!function($){
    'use strict';

    let GUIControlLines = function(element, options){
        let $this = this;

        this.$element = $(element);
        this.options = $.extend({}, GUIControlLines.DEFAULTS, options);
        this.count = 0;
        this.$whitespace = $('.whitespace', this.$element.parent());
        this.$lines = $('<div class="lines" />');

        this.$lines.insertAfter(this.$whitespace);

        if(this.options.autosize){
            this.$element.parent().addClass('autosize');
        } else {
            this.$element.on('scroll', function(){
                $this.scroll();
            });
        }

        this.$element.on('keydown', function(){
            $this.update();
        }).on('keyup', function(e){
            $this.update();
            if(e.target.selectionStart === $this.$element.val().length){
                $this.$element[0].scrollTop = $this.$element[0].scrollHeight;
            }
        }).on('keypress', function(e){
            $this.keypress(e);
        });

        this.update();
    };

    GUIControlLines.prototype.scroll = function(){
        this.$lines.css({
            'margin-top': (-1*this.$element.scrollTop()) + "px"
        });
    };

    GUIControlLines.prototype.countLines = function(){
        return this.$element.val() ? this.$element.val().split(/\r?\n/).length : 1;
    };

    GUIControlLines.prototype.update = function(){
        if(this.count != this.countLines()){
            this.count = this.countLines();
            this.fillLinesNumbers();
        }
    };

    GUIControlLines.prototype.fillLinesNumbers = function(){
        this.$lines.empty();
        this.$whitespace.html(this.count);
        for(let i = 1; i <= this.count; i++){
            this.$lines.append('<div class="line">' + i + '</div>');
        }
    };

    GUIControlLines.prototype.keypress = function(e){
        if(e.which === 13 && this.options.max > 0 && this.count >= this.options.max){
            e.preventDefault();
        }
        this.update();
    };

    GUIControlLines.DEFAULTS = {
        max: 0,
        autosize: false
    };

    $.fn.GUIControlLines = function(option){
        $.each(this, function(){
            let $this   = $(this);
            let data    = $this.data('gui.lines');
            let options = typeof option == 'object' && option;

            if(!data) $this.data('gui.lines', new GUIControlLines(this, options));
        });
    };

}(jQuery);