import $ from 'jquery';
import { isShiftPressed } from './lib/keycodes.js';

export class GuiControlChecklist {

    constructor(element){
        this.$element = $(element);
        this.$map = this.$element.find('input[type=checkbox]');
        this.lastCheckedIndex = 0;

        this.$element.find('[data-trigger=check],[data-trigger=uncheck]').on('click', e => {
            e.preventDefault();
            const $target = $(e.currentTarget);
            this.$map.each((i, e) => {
                this.check($(e), $target.data('trigger') == 'check');
            });

            this.lastCheckedIndex = 0;
        });

        this.$map.on('click', e => {
            if(isShiftPressed()){
                this._iterateCheck($(e.currentTarget));
            }

            this.lastCheckedIndex = this.$map.index($(e.currentTarget));
        });
    }

    check($checkbox, status){
        status = status || false;
        if($checkbox.is(':disabled') || $checkbox.prop('readonly')){
            return;
        }

        $checkbox.prop('checked', status).trigger('change.gui');
    }

    _iterateCheck($checkbox){
        let index = this.lastCheckedIndex;
        const currentIndex = this.$map.index($checkbox);
        const step = index > currentIndex ? -1 : 1;

        while(index !== currentIndex + step) {
            this.check(this.$map.eq(index), true);
            index += step;
        }

        $checkbox.blur();
    }
}

$.fn.GuiControlChecklist = function(){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.control');
        if(!data) $this.data('gui.control', new GuiControlChecklist(this));
    });
};

export default GuiControlChecklist;