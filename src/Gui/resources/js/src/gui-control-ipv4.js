import $ from 'jquery';
import { Keycodes } from './lib/keycodes.js';

export class GuiControlIpv4 {

    constructor(element){
        this.$element = $(element);
        this.$map = this.$element.find('input[type=text]');

        this.$element.parents('form-group').find('label[for=' + this.$element.attr('id') + ']')?.on('click', () => {
            this.$map.eq(0).focus();
        });

        this.$map.on('input', e => {
            this.validate($(e.currentTarget));
        }).on('keydown', e => {
            this._keyDown(e);
        }).on('paste', e => {
            this._paste(e);
        });
    }

    validate($input){
        let v = parseInt($input.val());

        if(isNaN(v)){
           $input.val('');
           return;
        }

        if(v < 0 || v > 255){
            if(v < 0){
                v = 0;
            } else if(v > 255) {
                v = 255;
            }
        }

        $input.val(v);

        if($input.val().length === 3){
            this._moveNext(this._getInputEq($input));
        }
    }

    _getInputEq($item){
        return this.$map.index($item);
    }

    _keyDown(e){
        const $target = $(e.currentTarget);
        const currentPosition = this._getInputEq($target);
        const selectionStart = e.target.selectionStart;
        const selectionEnd = e.target.selectionEnd;
        const valueLength = $target.val().length;

        if(e.keyCode === Keycodes.DECIMAL_POINT || e.keyCode === Keycodes.PERIOD){
            e.preventDefault();
            if(valueLength > 0){
                this._moveNext(currentPosition);
            }
        } else if(e.keyCode === Keycodes.BACKSPACE && currentPosition > 0 && valueLength === 0){
            this._movePrevious(currentPosition, e, true);
        } else if(e.keyCode === Keycodes.DELETE && currentPosition < (this.$map.length - 1) && valueLength === 0){
            this._moveNext(currentPosition, e, true);
        } else {
            if(e.keyCode === Keycodes.ARROW_LEFT && currentPosition > 0 && selectionStart === 0){
                this._movePrevious(currentPosition, e);
            } else if(e.keyCode === Keycodes.ARROW_RIGHT && currentPosition < (this.$map.length - 1) && selectionEnd === valueLength){
                this._moveNext(currentPosition, e);
            }
        }
    }

    _paste(e){
        if(this.$element.attr('disabled') || this.$element.prop('readonly')){
            return;
        }

        const clipboardData = e.originalEvent?.clipboardData?.getData('text')?.split('.');

        if(!clipboardData || clipboardData.length < 4){
            return;
        }

        e.preventDefault();

        for(let i = 0; i < clipboardData.length; i++){
            this.$map.eq(i)?.val(clipboardData[i])?.trigger('input').focus();
        }
    }

    _moveNext(currentPosition, event, positionCursor = false){
        if(event){
            event.preventDefault();
        }

        const $nextInput = this.$map.eq(currentPosition + 1);

        if(positionCursor){
            $nextInput?.focus()?.get(0)?.setSelectionRange(0, 0);
        } else {
            $nextInput?.select();
        }
    }

    _movePrevious(currentPosition, event, positionCursor = false){
        if(event){
            event.preventDefault();
        }

        const $prevInput = this.$map.eq(currentPosition - 1);

        if(positionCursor){
            $prevInput?.focus();
            const length = $prevInput?.val().length ?? 0;
            $prevInput[0]?.setSelectionRange(length, length);
        } else {
            $prevInput?.select();
        }
    }
}

$.fn.GuiControlIpv4 = function(){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.control');
        if(!data) $this.data('gui.control', new GuiControlIpv4(this));
    });
};

export default GuiControlIpv4;