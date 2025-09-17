import $ from 'jquery';
import { Keycodes } from './lib/keycodes.js';

export class GuiControlLines {

    static DEFAULTS = {
        max: 0
    }

    constructor(element, options){
        this.count = 0;
        this.options = $.extend({}, GuiControlLines.DEFAULTS, options);
        this.$element = $(element);
        this.$map = this.$element.parent().find('.lines');
        this.$placeholder = this.$element.parent().find('.width-placeholder');

        this.options.max = this.options.max !== null ? parseFloat(this.options.max) : 0;

        if(!this.$element.parent().hasClass('autosize')){
            this.$element.on('scroll', () => {
                this._scroll();
            });
        }

        this.$element.on('input', e => {
            this._update();
            const element = this.$element[0];
            if(e.currentTarget.selectionStart === element.value.length){
                element.scrollTop = element.scrollHeight;
            }
        }).on('keydown', e => {
            if(e.keyCode === Keycodes.ENTER && this.options.max > 0 && this.count >= this.options.max){
                e.preventDefault();
            }
        });

        this._update();
    }

    countLines(){
        const value = this.$element.val();

        if (!value || value === '') {
            return 1;
        }

        return value.split(/\r?\n/).length;
    }

    _scroll(){
        this.$map.css({
            'margin-top': (-1*this.$element.scrollTop()) + 'px'
        });
    }

    _update(){
        const count = this.countLines();
        if(this.count != count){
            this.count = count;
            this._updateLines();
        }
    }

    _updateLines(){
        this.$map.empty();
        this.$placeholder.html(this.count);
        for(let i = 1; i <= this.count; i++){
            this.$map.append('<div class="line">' + i + '</div>');
        }
    }
}

$.fn.GuiControlLines = function(option){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.control');
        const options = typeof option == 'object' && option;
        if(!data) $this.data('gui.control', new GuiControlLines(this, options));
    });
};

export default GuiControlLines;