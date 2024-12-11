!function($){
    'use strict';

    let GUIModal = function(element, options){
        let $this = this;

        this.$element = $(element);
        this.options = $.extend({}, GUIModal.DEFAULTS, $.extend({}, this.$element.data(), options));
        this.modal = bootstrap.Modal.getOrCreateInstance(this.options.modalTarget, { backdrop: this.options.modalBackdrop, keyboard: this.options.modalKeyboard });
        this.$modal = $(this.options.modalTarget);
        this.$modal.data('gui.modal', this);

        this.$element.on('click', function(e){
            e.preventDefault();
            $this.show();
        });

        this.$modal.on('hidden.bs.modal', function(){
            $('.modal-dialog', $this.$modal).removeClass(['modal-sm', 'modal-md', 'modal-lg', 'modal-xl', 'modal-xxl']);
        })
    };

    GUIModal.prototype._load = function(){
        let $this = this, timeout = 0;
        let isError = false;

        $.ajax({
            cache: false,
            url: this.options.modalUrl,
            headers: {"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            beforeSend: function(){
                $('.modal-content', $this.$modal).html('');
                timeout = setTimeout(function(){
                    $('.modal-dialog', $this.$modal).addClass('loading');
                    $('.modal-content', $this.$modal).html('<div class="loading" />');
                }, 300);
            },
            success: function(output){
                clearTimeout(timeout);
                $('.modal-content', $this.$modal).html(output);
            },
            complete: function(){
                clearTimeout(timeout);
                let dialog = $('.modal-dialog', $this.$modal).removeClass('loading');

                if(!isError){
                    dialog.removeClass(['modal-sm', 'modal-md', 'modal-lg', 'modal-xl', 'modal-xxl']);
                }

                dialog.addClass($this.options.modalClass);
            },
            error: function(xhr){
                if(xhr.status !== 404){
                    isError = true;
                }
                clearTimeout(timeout);
                $this.printError(xhr);
                gui.init($('.modal-content', $this.$modal));
            }
        });
    }

    GUIModal.prototype.printError = function(xhr){
        if(xhr.status === 500 && xhr.responseText.indexOf('<!DOCTYPE html') !== -1){
            gui.ignitionErrorFrame(xhr.responseText, $('.modal-content', this.$modal));
            this.options.modalClass = null;
        } else {
            $('.modal-content', this.$modal).html(xhr.responseText);
            this.options.modalClass = 'modal-md';
        }
    };

    GUIModal.prototype.show = function(){
        let $this = this;

        if(this.$element.attr('data-bs-toggle') == 'tooltip'){
            let tooltip = bootstrap.Tooltip.getOrCreateInstance(this.$element[0]);
            tooltip.hide();
        }

        if(this.options.modalUrl){
            $(this.options.modalTarget).on('show.bs.modal.gui', function(){
                $this._load();
            });
        } else {
            $('.modal-dialog', this.$modal).addClass(this.options.modalClass);
        }

        $(this.options.modalTarget).on('hidden.bs.modal.gui', function(){
            $('.modal-dialog', $this.$modal).removeClass($this.options.modalClass);
            if($this.options.modalUrl){
                $('.modal-content', $(this)).html('');
            }
            $(this).off('show.bs.modal.gui');
            $(this).off('hidden.bs.modal.gui');
        });

        this.modal.show();
    };

    GUIModal.prototype.hide = function(){
        this.modal.hide();
    };

    GUIModal.DEFAULTS = {
        modalBackdrop: 'static',
        modalKeyboard: false,
        modalTarget: '#gui_modal',
        modalClass: '',
        modalUrl: null
    };

    $.fn.GUIModal = function(option){
        return $.each(this, function(){
            let $this   = $(this);
            let data    = $this.data('gui.modal.initiator');
            let options = typeof option == 'object' && option;

            if(!data) $this.data('gui.modal.initiator', (new GUIModal(this, options)));
        });
    };

}(jQuery);