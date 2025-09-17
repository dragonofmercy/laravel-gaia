import $ from 'jquery';
import { Keycodes } from './lib/keycodes.js';

export class GuiControlCode {

    static DEFAULTS = {
        pattern: /[a-z0-9]/i
    }

    constructor(element, options){
        this.options = $.extend({}, GuiControlCode.DEFAULTS, options);
        this.$element = $(element);
        this.$map = this.$element.find('input[type=text]');

        this.$element.parents('form-group').find('label[for=' + this.$element.attr('id') + ']')?.on('click', () => {
            this.$map.eq(0).focus();
        });

        this.$map.on('focus', e => {
            $(e.currentTarget).trigger('select');
        }).on('input', e => {
            this.validate($(e.currentTarget));
        }).on('keydown', e => {
            this._keyDown(e);
        }).on('paste', e => {
            this._paste(e);
        });
    }

    validate($target){
        if(!$target.val()?.match(this.options.pattern)){
            $target.val('');
            return;
        }

        const currentPosition = this._getInputEq($target);
        const mapLength = this.$map.length;

        if(currentPosition < (mapLength - 1)){
            this.$map.eq(currentPosition + 1).focus();
        } else {
            if(this.$map.filter(function(){ return $(this).val() }).length === mapLength){
                this.$element.trigger('complete.gui');
                $target.blur();
            }
        }
    }

    _getInputEq($item){
        return this.$map.index($item);
    }

    _keyDown(e){
        const $target = $(e.currentTarget);
        const currentPosition = this._getInputEq($target);

        if(e.keyCode === Keycodes.BACKSPACE && currentPosition > 0){
            e.preventDefault();
            $target.val('');
            this.$map.eq(currentPosition - 1).focus()
        } else if(e.keyCode === Keycodes.DELETE && currentPosition < (this.$map.length - 1)){
            e.preventDefault();
            $target.val('');
            this.$map.eq(currentPosition + 1).focus();
        } else {
            if(e.keyCode === Keycodes.ARROW_LEFT && currentPosition > 0){
                e.preventDefault();
                this.$map.eq(currentPosition - 1).focus();
            } else if(e.keyCode === Keycodes.ARROW_RIGHT && currentPosition < (this.$map.length - 1)){
                e.preventDefault();
                this.$map.eq(currentPosition + 1).focus();
            }
        }
    }

    _paste(e){
        e.preventDefault();
        if(this.$element.attr('disabled') || this.$element.prop('readonly')){
            return;
        }

        const clipboardData = e.originalEvent?.clipboardData?.getData('text');

        if(!clipboardData){
            return;
        }

        for(let i = 0; i < clipboardData.length; i++){
            if(clipboardData.charAt(i).match(this.options.pattern)){
                this.$map.eq(i)?.val(clipboardData[i]).trigger('input');
            }
        }
    }
}

$.fn.GuiControlCode = function(option){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.control');
        const options = typeof option == 'object' && option;
        if(!data) $this.data('gui.control', new GuiControlCode(this, options));
    });
};

export default GuiControlCode;