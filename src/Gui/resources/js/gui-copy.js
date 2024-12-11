!function($){
    'use strict';

    let GUICopy = function(element, options){
        let $this = this;

        this.$element  = $(element);
        this.options   = $.extend({}, GUICopy.DEFAULTS, $.extend({}, this.$element.data(), options));

        if(typeof this.options.copyTarget === 'string' || this.options.copyTarget instanceof String){
            this.options.copyTarget = $(this.options.copyTarget);
        } else {
            this.options.copyTarget = this.$element;
        }

        if(typeof this.options.copyTooltipTarget === 'string' || this.options.copyTooltipTarget instanceof String){
            this.options.copyTooltipTarget = $(this.options.copyTooltipTarget);
        }

        if(navigator.clipboard){
            this.$element.on('click', function(e){
                e.preventDefault();
                $this.copy();
            });
        } else {
            if(this.$element.hasClass('btn')){
                this.$element.addClass('disabled');
            } else {
                this.$element.addClass('api-error');
            }
        }
    };

    GUICopy.prototype.copy = function(){
        if(navigator.clipboard){
            navigator.clipboard.writeText(this.getContent()).then(() => {
                if(this.options.copyTooltip){
                    let target = this.options.copyTooltipTarget ? this.options.copyTooltipTarget : this.options.copyTarget;
                    let tooltip = new bootstrap.Tooltip(target.get(0), {
                        title: GUI_LANGUAGE['guiCopyNotification'],
                        animation: false,
                        trigger: 'manual'
                    });
                    tooltip.show();
                    setTimeout(function(){
                        tooltip.dispose();
                    }, 500);
                }
            });
        } else {
            alert("Copy API not available");
        }
    };

    GUICopy.prototype.getContent = function(){
        if(this.options.copyTarget.is('input')){
            return this.options.copyTarget.val();
        } else if(this.options.copyHtml) {
            return this.options.copyTarget.html();
        } else {
            if(this.options.copyContent){
                return this.options.copyContent;
            }
            return this.options.copyTarget.get(0).innerText;
        }
    }

    GUICopy.DEFAULTS = {
        copyContent: null,
        copyTarget: null,
        copyTooltip: true,
        copyTooltipTarget: null,
        copyHtml: false
    };

    $.fn.GUICopy = function(option){
        return $.each(this, function(){
            let $this   = $(this);
            let data    = $this.data('gui.copy');
            let options = typeof option == 'object' && option;

            if(!data) $this.data('gui.copy', new GUICopy(this, options));
        });
    };

}(jQuery);