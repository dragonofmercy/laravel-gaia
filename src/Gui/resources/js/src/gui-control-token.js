import $ from 'jquery';
import Sortable from 'sortablejs';
import { Sifter } from '@orchidjs/sifter';
import { Autocomplete } from "./lib/autocomplete.js";
import { textWidth } from "./lib/text-width.js";
import { Keycodes } from "./lib/keycodes.js";

export class GuiControlToken extends Autocomplete {

    static DEFAULTS = {
        max: null,
        mode: 'row',
        sortable: false,
        delimiters: [],
        searchConjunction: 'and',
        searchRespectWordBoundaries: false
    }

    constructor(element, options){
        super(element, options);

        this.options = $.extend({}, GuiControlToken.DEFAULTS, this.options);
        this.options.validateKeys = this.options.validateKeys.concat(this.options.delimiters);

        this.sifter = new Sifter({}, { diacritics: true });

        this.$container = this.$element.parent().find('.token-container');
        this.$container.addClass('layout-' + this.options.mode);

        this.$input = $('<input class="search" type="text" autocomplete="off" />');
        this.$input.on('input.gui blur.gui', () => {
            this._resizeInput();
        });
        this.$input.appendTo(this.$container);
        this._bindEvents();

        this.$element.on('token_add.gui, token_remove.gui', () => {
            this._setSearchVisiblity(!this._limitReached())
        }).find('option:selected').each((_, option) => {
            this.insertToken($(option).attr('value'), $(option).html(), false);
        });

        this.$container.on('click.gui', '.btn-close', e => {
            e.preventDefault();
            this.removeToken($(e.currentTarget).parents('.token').attr('data-value'));
        }).on('focusout', () => {
            this._resetPending();
        });

        this._setSearchVisiblity(!this._limitReached());
        this._resizeInput();

        if(this.options.sortable){
            this.$container.addClass('sortable');

            new Sortable(this.$container[0], {
                handle: '.token>span',
                draggable: '.token',
                animation: 100,
                forceFallback: true,
                onSort: () => {
                    this._reorder();
                }
            });
        }

        setTimeout(() => {
            this.$element.trigger('initialized.gui');
        });
    }

    insertToken(value, text, reorder = true){
        const $token = $('<div class="token"><span></span><a class="btn-close"></a>');

        $token.find('span').html(text);
        $token.attr('data-value', value);

        this.$input.before($token);
        this.$element.trigger('token_add', [value, text]);

        if(reorder){
            this._reorder(false);
        }
    }

    removeToken(value){
        this.$container.find('.token[data-value="' + value + '"]')?.remove();
        this.$element.find('option[value="' + value + '"]')?.removeAttr('selected');
        this.$element.trigger('token_remove', [value]);

        this._reorder(false);
        this._setInputFocus();
    }

    _keyDown(e){
        super._keyDown(e);

        if(e.keyCode == Keycodes.BACKSPACE){
            if(this._getSearchElement().val() === ''){
                e.preventDefault();
                const $token = this.$container.find('.token:last');
                if($token.length > 0){
                    if($token.hasClass('await')){
                        this.removeToken($token.attr('data-value'));
                    } else {
                        $token.addClass('await');
                    }
                    return;
                }
            }
        }

        this._resetPending();
    }

    _resetPending(){
        this.$container.find('.token.await').removeClass('await');
    }

    _setSearchVisiblity(visible){
        if(visible && this.$container.find('.search').length === 0){
            this.$input.appendTo(this.$container);
        } else if(!visible && this.$container.find('.search').length > 0){
            this.$input.detach();
        }
    }

    _limitReached(){
        if(null === this.options.max){
            return false;
        }

        return this.$container.find('.token').length >= this.options.max;
    }

    _getSearchElement(){
        return this.$input;
    }

    _getReferenceItem(){
        if(this.options.reference === 'self'){
            return this.$container;
        } else {
            return this.$container.parents(this.options.reference);
        }
    }

    _resizeInput(){
        const width = textWidth(this.$input.val(), this.$input[0]);

        if(width < this.$container.width()){
            this.$input.css('min-width', width);
        }
    }

    _search(searchTerm){
        const options = [], results = [];
        const query = String(searchTerm).trim().normalize("NFD").replace(/[\u0300-\u036f]/g, "");

        $('option', this.$element).not(':selected, :disabled').each(function(){
            options.push({
                value:$(this).attr('value'),
                text:$(this).html()
            });
        });

        this.sifter.items = options;
        const score = this.sifter.search(query, {
            fields: 'text',
            conjunction: this.options.searchConjunction,
            respect_word_boundaries: this.options.searchRespectWordBoundaries
        });

        if(score.items.length > 0){
            for(let i = 0; i < score.items.length; i++){
                results.push(options[score.items[i]['id']]);
            }
        }

        this._parseResults(results);
    }

    _reorder(triggerEvent = true){
        const tokenValues = [];
        const optionsMap = new Map();

        this.$container.find('.token').map((_, e) => {
            tokenValues.push(e.getAttribute('data-value'));
        });

        this.$element.find('option').each((_, option) => {
            const $option = $(option);
            optionsMap.set($option.attr('value'), $option);
        });

        const $fragment = $(document.createDocumentFragment());
        let hasReordering = false;

        for(const value of tokenValues) {
            const $option = optionsMap.get(value);
            if($option && $option.length) {
                $fragment.append($option);
                hasReordering = true;
            }
        }

        this.$element.find('option').each((_, option) => {
            const $option = $(option);
            if(!tokenValues.includes($option.attr('value'))) {
                $fragment.append($option);
            }
        });

        this.$element.empty().append($fragment);

        if(hasReordering && triggerEvent){
            this.$element.trigger('token_reordered');
        }
    }

    _extractValue(value, text){
        return text ?? value;
    }

    _validateValue(value, text){
        this._getSearchElement().val("");

        const $option = this.$element.find('[value="' + value + '"]');

        if($option.length > 0){
            $option.attr('selected', 'selected');
            this.insertToken(value, text);
        }
    }

    _setInputFocus(){
        this._getSearchElement().focus();
    }
}

$.fn.GuiControlToken = function(option){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.control');
        const options = typeof option == 'object' && option;
        if(!data) $this.data('gui.control', new GuiControlToken(this, options));
    });
};

export default GuiControlToken;