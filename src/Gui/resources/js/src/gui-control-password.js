import $ from 'jquery';
import { Popover } from 'bootstrap';

export class GuiControlPassword {
    static DEFAULTS = {
        min: 8,
        max: 32,
        chars: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.-@',
        regex: '^[A-Za-z0-9]+(?=.*[0-9])(?=.*[a-z])(?=.*[.@-])(?=.*[A-Z]).*[A-Za-z0-9]+$',
        passwordFontSize: 1.5,
        copyInField: 'auto'
    }

    constructor(element, options){
        this.$element = $(element);
        this.$container = this.$element.parent();
        this.$btnReveal = this.$container.find('.reveal');
        this.$btnGenerate = this.$container.find('.generator');

        this.options = $.extend({}, GuiControlPassword.DEFAULTS, options);

        if(this.$btnReveal.length){
            this.$btnReveal.find('input').on('change.gui.password', () => {
                this.$element.attr('type', this.$btnReveal.find('input').is(':checked') ? 'text' : 'password');
            });
        }

        if(this.$btnGenerate.length){
            this._initGenerator();
            this.$btnGenerate.on('click.gui.password', () => {
                this.popover.toggle();
            });
        }
    }

    generatePassword(){
        const regex = new RegExp(this.options.regex);
        let password = "", index = 0;

        do {
            password = this._randomize(this.$range.val(), this.options.chars);
            if(index > 200){
                return;
            } else {
                index++;
            }
        } while(regex.test(password) === false)

        return password;
    }

    _initGenerator(){
        this.popover = new Popover(this.$btnGenerate[0], {
            content: this.$container.next().html(),
            customClass: 'password-generator',
            animation: false,
            trigger: 'manual',
            placement: 'top',
            sanitize: false,
            html: true
        });

        this.$btnGenerate.on('shown.bs.popover', () => {
            if(this.$element.val()){
                this._setText(this.$element.val());
            } else {
                this.$btnRandomize.trigger('click');
            }

            gui.init(this.popover.tip);

            $(document).on('mousedown.gui.password touchend.gui.password', e => {
                if(!$(e.target).closest('.popover.password-generator').length && $('.popover.password-generator').is(":visible")) {
                    this.popover.hide();
                }
            });
        }).on('hidden.bs.popover', () => {
            $(document).off('mousedown.gui.password touchend.gui.password');
        }).on('inserted.bs.popover', () => {
            this._initGeneratorContent();
        });
    }

    _initGeneratorContent(){
        this.$generatorContainer = $(this.popover.tip);

        const { min, max } = this.options;
        const currentValue = this.$element.val();
        const $container = this.$generatorContainer;

        Object.assign(this, {
            $passwordInput: $container.find('.password-display'),
            $range: $container.find('.form-range'),
            $btnRandomize: $container.find('[data-trigger="randomize"]'),
            $btnChoose: $container.find('[data-trigger="choose"]')
        });

        this.$range
            .attr({ min, max })
            .val(currentValue ? currentValue.length :  Math.floor((min + max) * 0.5));

        $container.find('.range-min').html(min);
        $container.find('.range-max').html(max);

        this.$btnRandomize.on('click', () => this._setText(this.generatePassword()));
        this.$btnChoose.on('click', e => this._updatePassword($(e.currentTarget).data('copy')));
        this.$range.on('input', () => this.$btnRandomize.trigger('click'));
        this.$passwordInput.on('click', e => $(e.currentTarget).trigger('select'));
    }

    _setText(text){
        this._fitText(text);
        this.$passwordInput.val(text);
    }

    _randomize(length, chars){
        let password = '';

        for(let i=0; i < length; i++){
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }

        return password;
    }

    _fitText(text){
        let fontSize = this.options.passwordFontSize;
        const $measure = $('<span class="measure" />').css('font-size', fontSize + 'em').html(text);

        this.$generatorContainer.find('.gui-password-generator').append($measure);

        while($measure.width() > this.$passwordInput.width()){
            fontSize-= 0.01;
            $measure.css('font-size', fontSize + 'em');
        }

        $measure.remove();
        this.$passwordInput.css('font-size', fontSize + 'em');
    }

    _updatePassword(withCopy){
        withCopy = withCopy || false;

        if(this.options.copyInField !== 'none'){
            const copyField = this.options.copyInField === 'auto' ? this.$element.parents('form-group').next().find('.gui-control-password input').first() : $(this.options.copyInField);
            if(copyField.length){
                copyField.val(this.$passwordInput.val()).trigger('change.gui.password');
            }
        }

        if(withCopy){
            if(navigator.clipboard){
                navigator.clipboard.writeText(this.$passwordInput.val()).then(() => {});
            } else {
                console.error('Clipboard API not supported');
            }
        }

        this.$element.val(this.$passwordInput.val()).trigger('change.gui.password');
        this.popover.hide();
    }
}

$.fn.GuiControlPassword = function(option){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.control');
        const options = typeof option == 'object' && option;
        if(!data) $this.data('gui.control', new GuiControlPassword(this, options));
    });
};

export default GuiControlPassword;