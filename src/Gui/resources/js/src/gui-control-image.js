import $ from 'jquery';
import Cropper from 'cropperjs';
import CanevaScale from './lib/caneva-scale.js';
import { Modal } from 'bootstrap';

export class GuiControlImage {

    static DEFAULTS = {
        fileTypes: 'image/png,image/jpeg',
        accepts: '.png,.jpg,.jpeg',
        exportType: 'image/jpeg',
        exportQuality: 1.0,
        height: 128,
        width: 128,
        viewMode: 1,
        guides: true
    }

    constructor(element, options){
        this.options = $.extend({}, GuiControlImage.DEFAULTS, options);
        this.$element = $(element);
        this.$container = this.$element.parent();
        this.$cropperImage = this.$container.find('.gui-cropper-image').on('load', () => this._displayModal());
        this.$thumbnail = this.$container.find('.thumbnail');

        this.$container.find('[data-trigger="clear"]').on('click', e => this.clear(e));
        this.$container.find('[data-trigger="browse"]').on('click', e => this.browse(e));

        this._bindLabel();
        this._updatePreview();
    }

    clear(event){
        if(event){
            $(event.currentTarget).blur().data('bs-tooltip').hide();
        }

        this.$element.val('');
        this._updatePreview();
    }

    browse(event){
        if(event){
            $(event.currentTarget).blur().data('bs-tooltip').hide();
        }

        $('<input type="file" />').attr('accept', this.options.accepts).on('change', e => this._loadImage(e.currentTarget)).trigger('click');
    }

    getModalSelector(){
        return '#' + this.$element.attr('id') + '_modal';
    }

    _bindLabel(){
        this.$element.parents('form-group').find('label[for=' + this.$element.attr('id') + ']')?.on('click', () => {
            this.browse();
        });
    }

    _loadImage(currentTarget){
        if(currentTarget.files.length < 1){
            return;
        }

        const file = currentTarget.files[0];

        if(this.options.fileTypes.split(',').indexOf(file.type) === -1){
            return;
        }

        $(currentTarget).remove();

        this.$cropperImage.attr('src', URL.createObjectURL(file));
        this.$thumbnail.addClass('loading-active');
        this.$modal = $(this.getModalSelector())
            .on('shown.bs.modal.cropper', e => this._modalShown(e))
            .on('hidden.bs.modal.cropper', e => this._modalHidden(e));
    }

    _displayModal(){
        this.modal = Modal.getOrCreateInstance(this.getModalSelector(), {
            keyboard: false,
            backdrop: 'static',
            focus: false
        });

        this.modal.show();
    }

    _modalShown(e){
        $(e.currentTarget).off('shown.bs.modal.cropper');

        this.cropper = new Cropper(this.$modal.find('.gui-cropper-image')[0], {
            viewMode: this.options.viewMode,
            aspectRatio: this.options.width / this.options.height,
            guides: this.options.guides,
            dragMode: 'move',
            autoCropArea: 1,
            rotatable: false,
            responsive: true,
            restore: true,
            ready: () => {
                this.$modal.find('[data-trigger=crop]').on('click.gui.cropper', () => this._crop());
                this.$modal.find('[data-trigger=reset]').on('click.gui.cropper', () => this.cropper.reset());
            },
        });
    }

    _modalHidden(e){
        $(e.currentTarget).off('hidden.bs.modal.cropper');

        this.cropper?.destroy();
        this.$cropperImage.attr('src', '');
        this.$modal.find('[data-trigger=reset]').off('click.gui.cropper');
        this.$modal.find('[data-trigger=crop]').off('click.gui.cropper').data('gui.button')?.reset();
        this.$thumbnail.removeClass('loading-active');
    }

    _updatePreview(blob){
        const imageValue = blob ?? this.$element.val();
        this.$thumbnail.css('background-image', imageValue ? 'url(' + imageValue + ')' : '');
    }

    _crop(){
        let img = new Image();

        img.onload = () => {
            let canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const scale = this.options.height / img.height;

            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            if(scale < 1){
                canvas = (new CanevaScale(canvas)).scale(scale);
            }

            canvas.toBlob(blob => {
                this._updatePreview(URL.createObjectURL(blob));
                this.$element.val(canvas.toDataURL(this.options.exportType, this.options.exportQuality));
                this.modal.hide();
            });

            img = null;
        }

        this.cropper.getCroppedCanvas().toBlob(blob => {
            img.src = URL.createObjectURL(blob);
        });
    }

}

$.fn.GuiControlImage = function(option){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.control');
        const options = typeof option == 'object' && option;
        if(!data) $this.data('gui.control', new GuiControlImage(this, options));
    });
};

export default GuiControlImage;