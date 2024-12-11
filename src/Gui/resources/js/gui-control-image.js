!function($){
    'use strict';

    let GUIControlImage = function(element, options){
        let $this = this;

        this.$element = $(element);
        this.options = $.extend({}, GUIControlImage.DEFAULTS, options);
        this.cache = this.$element.val();
        this.$container = this.$element.parent();
        this.$thumbnail = $('.thumbnail', this.$container);
        this.$modal = $('<a />').attr('data-modal-target', '#gui_modal').GUIModal({modalClass: "modal-dialog-centered modal-dialog-scrollable"});
        this.$modalContent = $('<div class="modal-body"><div class="cropper-crop-container"></div>' +
            '</div>' +
            '<div class="modal-footer d-flex justify-content-between">' +
            '<button type="button" class="btn btn-default btn-icon" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i>' + this.options.strings['btnClose'] + '</button>' +
            '<div class="d-flex gap-3">' +
            '<button type="button" class="btn btn-default btn-icon" data-trigger="reset"><i class="fa-solid fa-clock-rotate-left"></i>' + this.options.strings['btnReset'] + '</button>' +
            '<button type="button" class="btn btn-default btn-icon" data-trigger="crop" data-loading-text="' + this.options.strings['loading'] + '"><i class="fa-solid fa-crop-simple"></i>' + this.options.strings['btnCrop'] + '</button>' +
            '</div>' +
            '</div>');

        if(typeof this.options.fileTypes == 'object'){
            this.options.fileTypes = Object.values(this.options.fileTypes);
        }

        $('.control a', this.$container).on('click', function(e){
            e.preventDefault();

            if($(this).data('trigger') === 'upload'){
                $this.upload();
            }

            if($(this).data('trigger') === 'clear'){
                $this.clear();
            }
        });

        this.updatePreview();
    };

    GUIControlImage.prototype.updatePreview = function(url){
        url = url || this.$element.val();

        if(url){
            this.$thumbnail.removeClass('empty').css('background-image', 'url(' + url + ')');
        } else {
            this.$thumbnail.addClass('empty').css('background-image', '');
        }
    };

    GUIControlImage.prototype.upload = function(){
        let $this = this;
        let uploader = $('<input type="file" accept="' + $this.options.accepts + '" />');

        uploader.on('change', function(){
            if($(this)[0].files.length){
                $this.$thumbnail.addClass('loading');
                let filetype = $(this)[0].files[0]['type'];
                if($this.options.fileTypes.indexOf(filetype) === -1){
                    $this.$thumbnail.removeClass('loading');
                    return;
                }

                let $content = $this.$modalContent.clone();
                let $preview = $('<img class="gui-cropper-image" src />').attr('src', (URL || webkit).createObjectURL($(this)[0].files[0]));

                $preview.on('load', function(){
                    $('#gui_modal').on('show.bs.modal.cropper', function(){
                        $('#gui_modal').off('show.bs.modal.cropper');
                        $('#gui_modal .modal-dialog').addClass('loading');
                        $('#gui_modal .modal-content').html($content);
                        $('#gui_modal .modal-content .cropper-crop-container').append($preview);
                        gui.init('#gui_modal .modal-footer');
                    });

                    $('#gui_modal').on('shown.bs.modal.cropper', function(){
                        $('#gui_modal').off('shown.bs.modal.cropper');

                        let cropper = new Cropper($('#gui_modal .modal-content .gui-cropper-image')[0], {
                            viewMode: $this.options.viewMode,
                            aspectRatio: $this.options.width / $this.options.height,
                            guides: false,
                            dragMode: 'move',
                            autoCropArea: 1,
                            rotatable: false,
                            responsive: true,
                            restore: true,
                            ready: function(){
                                $('.loading', $content).remove();
                                $('#gui_modal .modal-dialog').removeClass('loading');
                                $('#gui_modal .modal-content button[data-trigger=crop]').on('click', function(){
                                    $this.crop(cropper);
                                });

                                $('#gui_modal .modal-content button[data-trigger=reset]').on('click', function(){
                                    cropper.reset();
                                });
                            }
                        });
                    });

                    $('#gui_modal').on('hidden.bs.modal.cropper', function(){
                        $('#gui_modal').off('hidden.bs.modal.cropper');
                        $('#gui_modal .modal-content').html('');
                        $this.$thumbnail.removeClass('loading');
                    });
                    $this.$modal.data('gui.modal.initiator').show();
                });
            }
        });

        uploader.trigger('click');
    };

    GUIControlImage.prototype.crop = function(c){
        let $this = this;
        let img = new Image();

        img.onload = function(){
            let canvas = document.createElement('canvas');
            let canvasCtx = canvas.getContext("2d");
            let scale = $this.options.height / img.height;

            canvas.width = img.width;
            canvas.height = img.height;
            canvasCtx.drawImage(img, 0, 0, canvas.width, canvas.height);

            if(scale < 1){
                canvas = gui.canvasScale(canvas, scale);
            }

            canvas.toBlob((blob) => {
                $this.updatePreview((URL || webkit).createObjectURL(blob));
                $this.$element.val(canvas.toDataURL());
                $this.$modal.data('gui.modal.initiator').hide();
            });
        }

        c.getCroppedCanvas().toBlob((blob) => {
            img.src = (URL || webkit).createObjectURL(blob);
        });
    };

    GUIControlImage.prototype.clear = function(){
        this.$element.val('');
        this.updatePreview();
    };

    GUIControlImage.DEFAULTS = {
        height: 128,
        width: 128,
        viewMode: 1,
        antialising: 0.8,
        fileTypes: ['image/png', 'image/jpeg'],
        accepts: '.png,.jpg,.jpeg',
        strings: { btnClose: 'Close', btnCrop: 'Crop', btnReset: 'Reset', loading: 'Loading...' }
    };

    $.fn.GUIControlImage = function(option){
        $.each(this, function(){
            let $this = $(this);
            let data = $this.data('gui.image');
            let options = typeof option == 'object' && option;

            if(!data) $this.data('gui.image', new GUIControlImage(this, options));
        });
    };

}(jQuery);