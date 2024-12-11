!function($){
    'use strict';

    let GUIControlPassword = function(element, options){
        let $this = this;

        this.$element = $(element);
        this.options = $.extend({}, GUIControlPassword.DEFAULTS, options);
        this.$container = this.$element.parent();
        this.$btnShowHide = $('button[data-toggle=toggle]', this.$container);
        this.$btnGenerate = $('button[data-toggle=generator]', this.$container);

        if(this.$btnShowHide.length){
            this.$btnShowHide.on('click', function(){
                $this.toggleType($('i', $(this)));
            });
        }

        if(this.$btnGenerate.length){
            this.initGenerator();
            this.$btnGenerate.on('click', function(){
                $this.popover.toggle();
            });
        }
    };

    GUIControlPassword.prototype.toggleType = function($icon){
        if($icon.attr('class') === this.options.iconReveal){
            this.$element.attr('type', 'text');
            $icon.attr('class', this.options.iconHide);
        } else {
            this.$element.attr('type', 'password');
            $icon.attr('class', this.options.iconReveal);
        }
    };

    GUIControlPassword.prototype.initGenerator = function(){
        let $this = this;

        this.popover = new bootstrap.Popover(this.$btnGenerate[0], {
            content: this.$container.next().html(),
            title: this.options.strings.generatorTitle,
            customClass: 'password-generator',
            animation: false,
            trigger: 'manual',
            placement: 'top',
            sanitize: false,
            html: true
        });

        this.$btnGenerate.on('shown.bs.popover', function(){
            if($this.$element.val()){
                $this.setText($this.$element.val());
            } else {
                $this.$generatorBtnRandom.trigger('click');
            }

            $(document).on('mousedown.gui.password touchend.gui.password', function(e){
                if(!$(e.target).closest('.popover.password-generator').length && $('.popover.password-generator').is(":visible")) {
                    $this.popover.hide();
                }
            });
        }).on('hidden.bs.popover', function(){
            $(document).off('mousedown.gui.password touchend.gui.password');
        }).on('inserted.bs.popover', function(){
            $this.initGeneratorContent();
        });
    };

    GUIControlPassword.prototype.initGeneratorContent = function(){
        let $this = this;

        this.$generatorContainer = $(this.popover.tip);
        this.$PasswordDisplay = $('.password-display', this.$generatorContainer);
        this.$range = $('.form-range', this.$generatorContainer);
        this.$generatorBtnRandom = $('button[data-toggle=random]', this.$generatorContainer);
        this.$generatorBtnChoose = $('button[data-toggle=choose]', this.$generatorContainer);

        this.$range.attr('min', this.options.min);
        this.$range.attr('max', this.options.max);

        if(this.$element.val()){
            this.$range.val(this.$element.val().length);
        } else {
            this.$range.val(Number((this.options.max + this.options.min) / 2, 0));
        }

        $('.range-min', this.$generatorContainer).html(this.options.min);
        $('.range-max', this.$generatorContainer).html(this.options.max);

        this.$range.GUIControlRange({tooltip: true});

        this.$generatorBtnRandom.on('click', function(){
            $this.setText($this.generateValidPassword());
        });

        this.$generatorBtnChoose.on('click', function(){
            $this.updatePassword($(this).data('copy'));
        });

        this.$range.on('input', function(){
            $this.$generatorBtnRandom.trigger('click');
        });

        this.$PasswordDisplay.on('click', function(){
            $(this).trigger('select');
        });
    };

    GUIControlPassword.prototype.updatePassword = function(with_copy){
        if(this.options.copyInField !== 'none'){
            let copyField = this.options.copyInField === 'auto' ? this.$element.parents('form-group').next().find('.gui-control-password input') : $(this.options.copyInField);

            if(copyField.length){
                copyField.val(this.$PasswordDisplay.val()).trigger('change.gui');
            }
        }

        if(with_copy){
            this.$PasswordDisplay.GUICopy({ tooltip: false }).data('gui.copy').copy();
        }

        this.$element.val(this.$PasswordDisplay.val()).trigger('change.gui');
        this.popover.hide();
    };

    GUIControlPassword.prototype.generateValidPassword = function(){
        let regexp = new RegExp(this.options.regex);
        let password = "";
        let index = 0;

        do {
            password = this.generateRandomPassword(this.$range.val(), this.options.chars);
            if(index > 200){
                return;
            } else {
                index++;
            }
        } while(regexp.test(password) === false);
        return password;
    };

    GUIControlPassword.prototype.generateRandomPassword = function(length, chars){
        let password = '';
        for(let i=0; i < length; i++){
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return password;
    };

    GUIControlPassword.prototype.setText = function(v){
        this.fitText(this.$PasswordDisplay, v);
        this.$PasswordDisplay.val(v);
    };

    GUIControlPassword.prototype.fitText = function($input, text){
        let fontSize = this.options.passwordFontSize;
        let $measure = $('<span class="measure" />').css('font-size', fontSize + 'em').html(text);

        $('.gui-password-generator', this.$generatorContainer).append($measure);

        while($measure.width() > $input.width()){
            fontSize-= 0.01;
            $measure.css('font-size', fontSize + 'em');
        }

        $measure.remove();
        $input.css('font-size', fontSize + 'em');
    };

    GUIControlPassword.DEFAULTS = {
        min: 8,
        max: 32,
        chars: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.-@',
        regex: '^[A-Za-z0-9]+(?=.*[0-9])(?=.*[a-z])(?=.*[.@-])(?=.*[A-Z]).*[A-Za-z0-9]+$',
        iconReveal: 'fa-regular fa-eye-slash',
        iconHide: 'fa-regular fa-eye',
        iconGenerate: 'fa-solid fa-gear',
        passwordFontSize: 1.5,
        copyInField: 'auto',
        strings: { generatorTitle: 'Password generator' }
    };

    $.fn.GUIControlPassword = function(option){
        $.each(this, function(){
            let $this   = $(this);
            let data    = $this.data('gui.password');
            let options = typeof option == 'object' && option;

            if(!data) $this.data('gui.password', new GUIControlPassword(this, options));
        });
    };

}(jQuery);