import LZString from 'lz-string';
import $ from 'jquery';

export class GuiTabs {
    static DEFAULTS = {
        remember: false
    }

    constructor(element, options) {
        this.$element = $(element);
        this.options = $.extend({}, GuiTabs.DEFAULTS, $.extend({}, this.$element.data(), options));
        this.id = this.$element.attr('id') || false;

        this.$element.find('.nav-item>a[data-target]').on('click.gui', (e) => {
            this.show(e.target.getAttribute('data-target'), e);
        });

        if(!this._showTabPaneInError()){
            if(this._hasId() && this.options.remember){
                this._showFromUrl();
            }

            if(this.$element.find('.nav-item>a.active').length === 0){
                this.show(this.$element.find('.nav-item>a[data-target]:first-child').attr('data-target'));
            }
        }
    }

    show(tabId, e = undefined) {
        const pane = $(`[data-pane="${tabId}"]`);

        $('.nav-item>a', this.$element).removeClass('active');
        $('.tab-pane.active', pane.parent()).removeClass('active');
        $(`[data-target="${tabId}"], [data-pane="${tabId}"]`).addClass('active');

        $(pane).trigger('shown.gui.tabs');

        if((e !== undefined) && this.options.remember && this._hasId()) {
            this._saveToUrl(tabId);
        }
    }

    _showTabPaneInError() {
        const errors = [];
        this.$element.find('.nav-item>a[data-target]').each(function(){
            const tabId = $(this).attr('data-target');

            if($(`[data-pane="${tabId}"] .has-error`).length > 0){
                $(`[data-target="${tabId}"]`).addClass('nav-link-error');
                errors.push(tabId);
            }
        })

        const hasError = errors.length > 0;

        if(hasError){
            this.show(errors[0]);
        }

        return hasError;
    }

    _hasId() {
        return this.id !== false;
    }

    _showFromUrl() {
        const json = this._decrypt(window.location.hash.substring(1));
        if(json[this.id] !== undefined){
            this.show(json[this.id]);
        }
    }

    _saveToUrl(tabId) {
        const json = this._decrypt(window.location.hash.substring(1));
        json[this.id] = tabId;
        location.hash = this._encrypt(json);
    }

    _encrypt(data) {
        return LZString.compressToEncodedURIComponent(JSON.stringify(data));
    }

    _decrypt(data) {
        try {
            const decompressed = LZString.decompressFromEncodedURIComponent(data);
            return decompressed ? JSON.parse(decompressed) : {};
        } catch(_) {
            return {};
        }
    }
}

$.fn.GuiTabs = function(option){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.tabs');
        const options = typeof option == 'object' && option;
        if(!data) $this.data('gui.tabs', new GuiTabs(this, options));
    });
};

export default GuiTabs;