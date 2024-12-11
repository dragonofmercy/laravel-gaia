!function($){
    'use strict';

    let GUIControlCode = function(element, options){
        let $this = this;

        this.$element  = $(element);
        this.$inputs = $('input', this.$element);
        this.options = $.extend({}, GUIControlCode.DEFAULTS, options);

        $('.control-label label', this.$element.parents('form-group')).on('click', function(){
            $this.$inputs.eq(0).trigger('focus');
        });

        this.$inputs.each(function(){
            let current = $(this);

            if($this.$element.attr('disabled') || $this.$element.attr('readonly')){
                $(this).prop('readonly', true);
            }

            $(this).on('input', function(e){
                $this.input(e);
            }).on('keydown', function(e){
                $this.keydown(e);
            }).on('focus', function(){
                current.trigger('select');
            }).on('paste', function(e){
                e.preventDefault();

                if($this.$element.attr('disabled') || $this.$element.attr('readonly')){
                    return false;
                }

                let content = e.originalEvent.clipboardData.getData('text').replace(/[^a-z0-9]/i, '');

                for(let i = 0; i < $this.$inputs.length; i++){
                    let $input = $this.$inputs.eq(i);
                    $input.val(content[i]);
                    $this.validate($input);
                }
            });
        });
    };

    GUIControlCode.prototype.input = function(e){
        this.validate($(e.target));
    };

    GUIControlCode.prototype.keydown = function(e){
        let $target = $(e.target);
        let position = this.getInputPosition($target);

        if(e.which === 8 && position > 0){
            e.preventDefault();
            $target.val('');
            this.$inputs.eq(position - 1).trigger('focus');
        } else if(e.which === 46 && position < (this.$inputs.length - 1)){
            e.preventDefault();
            $target.val('');
            this.$inputs.eq(position + 1).trigger('focus');
        } else {
            if(e.which === 37 && position > 0){
                e.preventDefault();
                this.$inputs.eq(position - 1).trigger('focus');
            } else if(e.which === 39){
                e.preventDefault();
                this.$inputs.eq(position + 1).trigger('focus');
            }
        }
    };

    GUIControlCode.prototype.getInputPosition = function($input){
        let index = $input.attr('name').match(/\[([0-9])]$/i);
        return parseInt(index !== null ? index[1] : 0);
    };

    GUIControlCode.prototype.validate = function($input){
        if($input.val().match(this.options.pattern)){
            if(this.getInputPosition($input) < (this.$inputs.length - 1)){
                this.$inputs.eq(this.getInputPosition($input) + 1).trigger('focus');
            } else {
                let complete = true;
                this.$inputs.each(function(){
                    if(!$(this).val()){
                        complete = false;
                    }
                });
                if(complete){
                    this.options.onComplete();
                }
                $input.trigger('blur');
            }
        } else {
            $input.val('');
        }
    };

    GUIControlCode.DEFAULTS = {
        pattern: /[a-z0-9]/i,
        onComplete: function(){}
    };

    $.fn.GUIControlCode = function(option){
        $.each(this, function(){
            let $this = $(this);
            let data = $this.data('gui.code');
            let options = typeof option == 'object' && option;

            if(!data) $this.data('gui.code', new GUIControlCode(this, options));
        });
    };

}(jQuery);