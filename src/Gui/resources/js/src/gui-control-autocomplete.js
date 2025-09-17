import $ from 'jquery';
import { Autocomplete } from "./lib/autocomplete.js";

export class GuiControlAutocomplete extends Autocomplete {

    static DEFAULTS = {
        provider: null,
        providerQueryParameter: 'term',
        cache: false,
        valueField: 'value',
        textField: 'text'
    }

    constructor(element, options){
        super(element, options);

        this.options = $.extend({}, GuiControlAutocomplete.DEFAULTS, this.options);
        this.xhr = null;

        if(this.$element.prev().is(':hidden')){
            this.$element.parents('form-group').find('label[for=' + this.$element.prev().attr('id') + ']')?.on('click', () => {
                const len = this.$element.val().length;
                this.$element.focus()[0]?.setSelectionRange(len, len);
            });
        }

        super._bindEvents();
    }

    _extractValue(value, text){
        if(text === undefined || text.length === 0){
            return value;
        }

        const valueText = $(text).find('[data-text]');

        if(valueText.length){
            return valueText[0].innerText;
        } else {
            return text;
        }
    }

    _validateValue(value, text){
        this._getSearchElement().val(text).trigger('change');
    }

    _search(searchTerm){
        const results = [];
        if(this.xhr !== undefined && this.xhr !== null){
            this.xhr.abort();
        }

        this.xhr = $.ajax(this.options.provider, {
            data: { [this.options.providerQueryParameter]: searchTerm },
            cache: this.options.cache,
            dataType: 'json',
            success: data => {
                if(data.length > 0){
                    $.each(data, (_, item) => {
                        const entry = { value: item[this.options.valueField], text: "" };

                        if(item[this.options.textField] !== undefined){
                            entry.text = item[this.options.textField];
                        } else {
                            entry.text = item[this.options.valueField];
                        }

                        results.push(entry);
                    })
                }

                this._parseResults(results);
            }
        });
    }
}

$.fn.GuiControlAutocomplete = function(option){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.control');
        const options = typeof option == 'object' && option;
        if(!data) $this.data('gui.control', new GuiControlAutocomplete(this, options));
    });
};

export default GuiControlAutocomplete;