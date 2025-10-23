import $ from 'jquery';
import Sortable from 'sortablejs';
import { isShiftPressed, Keycodes } from './lib/keycodes.js';

export class GuiControlMultiFields {

    static DEFAULTS = {
        max: null,
        sortable: true,
        newLineOnTab: true
    }

    constructor(element, options){
        this.options = $.extend({}, GuiControlMultiFields.DEFAULTS, options);
        this.$element = $(element);

        this.$element.on('click', '[data-trigger=add]', e => {
            e.preventDefault();
            this.newLine();
        });

        this.$element.on('click', '[data-trigger=remove]', e => {
            e.preventDefault();
            this.removeLine($(e.currentTarget).parents('tr'));
        });

        if(this.options.newLineOnTab){
            this.$element.on('keydown.gui', 'table>tbody>tr:last-child>td input:last', e => {
                if(e.keyCode === Keycodes.TAB && !isShiftPressed()){
                    e.preventDefault();
                    this.newLine();
                }
            });
        }

        if(this.options.sortable){
            new Sortable(this.$element.find('table>tbody')[0], {
                handle: '[data-trigger=move]',
                direction: 'vertical',
                ghostClass: "sortable-ghost",
                animation: 100,
                onChange: () => {
                    this.$element.trigger('lineMoved.gui')
                }
            });
        }

        this.$element.on('lineAdded.gui lineRemoved.gui lineMoved.gui initialized.gui', () => {
            this._buttonStates();
        });

        setTimeout(() => {
            this.$element.trigger('initialized.gui');
        });
    }

    newLine(force = false){
        if(this.options.max !== null && this.count() >= this.options.max && !force){
            return;
        }

        const $last = this.$element.find('table>tbody>tr:last-child');
        const $template = $last.clone();

        $template.find('input, select, textarea').removeClass('is-invalid').val('');
        $template.find('.invalid-feedback').remove();
        $template.insertAfter($last);

        gui.init($template);

        $template.find('input').first().focus();
        this.$element.trigger('lineAdded.gui');
    }

    removeLine($line){
        if(this.count() === 1){
            this.newLine(true);
        }

        $line.remove();

        this.$element.trigger('lineRemoved.gui');
    }

    count(){
        return this.$element.find('table>tbody>tr').length;
    }

    _buttonStates(){
        const $btnAdd = this.$element.find('[data-trigger=add]');
        const $btnMove = this.$element.find('[data-trigger=move]');

        if(this.options.max > 0 && this.count() >= this.options.max){
            $btnAdd.addClass('disabled');
        } else {
            $btnAdd.removeClass('disabled');
        }

        if(this.count() === 1){
            $btnMove.addClass('disabled');
        } else {
            $btnMove.removeClass('disabled');
        }
    }
}

$.fn.GuiControlMultiFields = function(option){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.control');
        const options = typeof option == 'object' && option;
        if(!data) $this.data('gui.control', new GuiControlMultiFields(this, options));
    });
};

export default GuiControlMultiFields;