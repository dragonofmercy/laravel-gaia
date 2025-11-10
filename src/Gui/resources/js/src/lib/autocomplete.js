import $ from "jquery";
import * as Popper from "@popperjs/core";
import escapeHtml from "escape-html";
import { isShiftPressed, Keycodes } from "./keycodes.js";

export class Autocomplete {

    static DEFAULTS = {
        minLength: 1,
        limit: 10,
        offset: 2,
        reference: 'self',
        autoselectFirstItem: true,
        validateKeys: [Keycodes.ENTER, Keycodes.TAB]
    }

    constructor(element, options){
        this.$element = $(element)
        this.options = $.extend({}, Autocomplete.DEFAULTS, options);
        this.popper = null;

        this.$template = $('<div class="dropdown-menu autocomplete show" />').on('mousedown touchstart', '[data-selectable]', e => {
            e.preventDefault();
            e.stopPropagation();

            if(e.button && e.button === 2){
                return;
            }

            this._dropdownSelect($(e.currentTarget));
        });
    }

    _bindEvents(){
        this._getSearchElement().on('keydown.gui', e => {
            this._keyDown(e);
        }).on('input.gui', () => {
            this._input();
        }).on('validate.gui', (e, value, text) => {
            this._validateValue(value, text);
        }).on('blur.gui', () => {
            this._dropdownClose();
        });

        if(this.options.openOnFocus){
            this._getSearchElement().on('focus.gui', () => {
                this._input();
            });
        }
    }

    _getSearchElement(){
        return this.$element;
    }

    isDropdownOpen(){
        return this.popper !== null;
    }

    _keyDown(e){
        switch(e.keyCode){
            case Keycodes.ESCAPE:
                e.preventDefault();
                this._dropdownClose();
                return;
            case Keycodes.ARROW_DOWN:
            case Keycodes.PAGE_DOWN:
                if(this.isDropdownOpen()){
                    e.preventDefault();
                    this._moveSelection(true);
                }
                return;
            case Keycodes.ARROW_UP:
            case Keycodes.PAGE_UP:
                if(this.isDropdownOpen()){
                    e.preventDefault();
                    this._moveSelection(false);
                }
                return;
        }

        if(this.options.validateKeys.includes(e.keyCode)){
            if(e.keyCode === Keycodes.TAB && isShiftPressed()){
                return;
            }

            if(this.isDropdownOpen()){
                e.preventDefault();
                this._dropdownSelect(this._getActiveItem());
            }
        }
    }

    _input(){
        if(this._getSearchElement().val().length >= this.options.minLength){
            this._search(this._getSearchElement().val().trim());
        } else {
            this._dropdownClose();
        }
    }

    _moveSelection(down){
        if(!this.isDropdownOpen()){
            return;
        }

        const activeItem = this._getActiveItem();

        if(activeItem.length > 0){
            if(activeItem.is(down ? '[data-selectable]:last' : '[data-selectable]:first')){
                this._setActiveItem(this.$template.find(down ? '[data-selectable]:first' : '[data-selectable]:last'));
            } else {
                if(down){
                    this._setActiveItem(activeItem.next());
                } else {
                    this._setActiveItem(activeItem.prev());
                }
            }
        } else {
            this._setActiveItem(this.$template.find(':first-child'));
        }
    }

    _setActiveItem($item){
        this._getActiveItem().removeClass('active');
        $item.addClass('active');
    }

    _getActiveItem(){
        return this.$template.find('a.active');
    }

    _parseResults(results){
        let numResults = results.length;

        if(numResults > 0 && !this.isDropdownOpen()){
            this._dropdownOpen();
        } else if(numResults === 0 && this.isDropdownOpen()){
            this._dropdownClose();
            return;
        }

        this.$template.empty();

        if(typeof this.options.limit === 'number'){
            numResults = Math.min(numResults, this.options.limit);
        }

        for(let i = 0; i < numResults; i++){
            const $item = $('<a class="dropdown-item" />');
            $item.html(results[i].text);
            $item.attr('data-value', escapeHtml(results[i].value));
            $item.attr('data-selectable', '');
            this.$template.append($item);
        }

        if(this.options.autoselectFirstItem){
            this._setActiveItem(this.$template.find('[data-selectable]:first'));
        }
    }

    _dropdownOpen(){
        $('body').append(this.$template);
        $(document).on('mousedown.gui.autocomplete touchend.gui.autocomplete', e => {
            if(!$(e.target).closest('.dropdown-menu').length && $('.dropdown-menu').is(":visible")) {
                this._dropdownClose();
            }
        });

        this.popper = Popper.createPopper(this._getReferenceItem()[0], this.$template[0], this._getPopperConfig());
    }

    _getReferenceItem(){
        if(this.options.reference === 'self'){
            return this.$element;
        } else {
            return this.$element.parents(this.options.reference);
        }
    }

    _dropdownClose(){
        if(!this.isDropdownOpen()){
            return;
        }
        this.popper.destroy();
        this.popper = null;
        this.$template.detach();
    }

    _dropdownSelect($target){
        const text = this._extractValue($target.data('value'), $target.html());

        this._getSearchElement().trigger('validate.gui', [$target.data('value'), text]);
        this._dropdownClose();
    }

    _getPopperConfig(){
        return {
            placement: 'bottom-start',
            modifiers: [{
                name: 'offset',
                options: {
                    offset: [0, this.options.offset]
                }
            },{
                name: 'minWidth',
                enabled: true,
                phase: "beforeWrite",
                fn({ state }) {
                    state.styles.popper.minWidth = `${state.rects.reference.width}px`
                }
            }]
        };
    }


    /**
     * Validates the provided value and text. This method is intended to be overridden by a subclass.
     *
     * @abstract
     * @param {*} value - The value to be validated.
     * @param {string} text - The accompanying text to be validated.
     * @return {void} This method does not return a value if implemented correctly, but throws an error if not overridden.
     */
    _validateValue(value, text){
        throw new Error('Method _validateValue() must be implemented by subclass');
    }

    /**
     * Performs a search operation based on the provided search term. This method is intended to be implemented
     * by subclasses to define specific search behavior.
     *
     * @abstract
     * @param {string} searchTerm - The term to search for.
     * @return {*} The result of the search operation. The specific return type and value depend on the subclass implementation.
     */
    _search(searchTerm){
        throw new Error('Method _search() must be implemented by subclass');
    }

    /**
     * Extracts a value based on the provided inputs. This method is meant to be implemented by a subclass.
     *
     * @abstract
     * @param {*} value - The value to be processed or extracted.
     * @param {string} text - The accompanying text to assist in extracting the value.
     * @return {*} The extracted value based on input parameters.
     * @throws {Error} If the method is not implemented by a subclass.
     */
    _extractValue(value, text){
        throw new Error('Method _extractValue() must be implemented by subclass');
    }
}