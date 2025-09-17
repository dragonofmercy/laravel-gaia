import $ from 'jquery';
import { Modal } from 'bootstrap';

export class GuiModal {
    static SELECTOR_DIALOG = '.modal-dialog'
    static SELECTOR_DIALOG_CONTENT = '.modal-content'
    static MODAL_ERROR_STRUCTURE = '<div class="modal-header"><h5 class="modal-title"></h5><a class="btn-close" data-bs-dismiss="modal"></a></div><div class="modal-body"></div><div class="modal-footer"></div>'

    static DEFAULTS = {
        target: '#gui-modal',
        backdrop: 'static',
        focus: true,
        keyboard: false,
        url: '',
        size: 'modal-md'
    }

    constructor(element, options){
        this.$element = $(element);
        this.options = $.extend({}, GuiModal.DEFAULTS, $.extend({}, this.$element.data(), options));

        this.$modalElement = $(this.options.target);
        this.$modalDialog = $(GuiModal.SELECTOR_DIALOG, this.$modalElement);
        this.$modalDialogContent = $(GuiModal.SELECTOR_DIALOG_CONTENT, this.$modalElement);

        if(this.options.url === '' && this.$element.attr('href')){
            const href = this.$element.attr('href');
            if(this._isValidUrl(href)){
                this.options.url = href;
            }
        }
    }

    show(){
        this._initBootstrapModal();
        this._addEventListeners();
        this._modal.show();
    }

    _initBootstrapModal(){
        this._modal = new Modal(this.$modalElement[0], {
            keyboard: this.options.keyboard,
            backdrop: this.options.backdrop,
            focus: this.options.focus
        });
    }

    _addEventListeners(){
        this.$modalElement.on('show.bs.modal', () => { this._modalShow() });
        this.$modalElement.on('hidden.bs.modal', () => { this._modalhidden() });
    }

    _removeEventListeners(){
        this.$modalElement.off('show.bs.modal');
        this.$modalElement.off('hidden.bs.modal');
    }

    _modalShow(){
        this._updateSize(this.options.size);

        if(this.options.url !== ''){
            this._request();
        } else {
            this.$modalElement.trigger('loaded.gui.modal');
        }
    }

    _modalhidden(){
        this._updateSize(null);
        this._removeEventListeners();

        this.$modalDialogContent.html('');
        this._modal.dispose();
    }

    _request(){
        const timeout = setTimeout(() => {
            this.$modalElement.addClass('loading-active');
        }, 100)

        $.ajax({
            url: this.options.url,
            dataType: 'text',
            success: (data) => {
                this.$modalDialogContent.html(data);
            },
            error: (e) => {
                if(e.status === 404){
                    this._updateSize('modal-lg');
                    this.$modalDialogContent.html(e.responseText);
                } else {
                    this._printError(e);
                }
            },
            complete: () => {
                clearTimeout(timeout);
                this.$modalElement.removeClass('loading-active');
                this.$modalElement.trigger('loaded.gui.modal');
                gui.init(this.$modalElement);
            }
        });
    }

    _updateSize(size){
        const acceptedSizes = ['modal-sm', 'modal-md', 'modal-lg', 'modal-xl', 'modal-fullscreen'];
        const customSizes = this.options.size.split(' ');

        for(const s of [...acceptedSizes, ...customSizes]){
            this.$modalDialog.removeClass(s);
        }

        if(null !== size){
            for(const r of size.split(' ')){
                this.$modalDialog.addClass(r);
            }
        }
    }

    _isValidUrl(string){
        try {
            if(string.startsWith('http://') || string.startsWith('https://')){
                new URL(string);
                return true;
            }

            return string.startsWith('/');
        } catch {
            return false;
        }
    }

    _printError(e){
        this.$modalDialogContent.html(GuiModal.MODAL_ERROR_STRUCTURE);
        const modalBody = $('.modal-body', this.$modalElement);

        $('.modal-title', this.$modalElement).html(`${e.status}: ${e.statusText}`);

        if(e.responseText.indexOf('<!DOCTYPE html') !== -1){
            const iframe = document.createElement('iframe');
            iframe.classList.add('modal-error-frame');
            iframe.srcdoc = e.responseText;

            modalBody.addClass('p-0');
            modalBody.html(iframe);

            this._updateSize('modal-xl');
        } else {
            modalBody.html(e.responseText);
            this._updateSize('modal-md');
        }
    }
}

$.fn.GuiModal = function(option){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.modal');
        const options = typeof option == 'object' && option;
        if(!data) $this.data('gui.modal', new GuiModal(this, options));
    });
};

export default GuiModal;