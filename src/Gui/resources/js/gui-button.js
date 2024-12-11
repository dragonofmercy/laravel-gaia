!function($){
    'use strict';

    let GUIButton = function(element, options){
        this.$element  = $(element);
        this.options   = $.extend({}, GUIButton.DEFAULTS, options);
        this.isLoading = false;
    };

    GUIButton.prototype.setState = function(state){
        let d    = 'disabled';
        let $el  = this.$element;
        let data = $el.data();
        let type = data['loadingType'] || this.options['loadingType'];

        state += 'Text';

        if(data.resetText == null) $el.data('resetText', $el.html());

        setTimeout(function(){
            $el.html(data[state] == null ? this.options[state] : data[state]);
            if(state == 'loadingText') {
                $el.wrapInner('<span />');
                $el.prepend('<span class="spinner-' + type + ' spinner-' + type + '-sm"></span>');
                $el.addClass(d).attr(d, d).prop(d, true).addClass('loading-active');
                this.isLoading = true;
            } else if(this.isLoading){
                this.isLoading = false;
                $el.removeClass(d).removeAttr(d).prop(d, false).removeClass('loading-active');
            }
        }.bind(this), 0);
    };

    GUIButton.DEFAULTS = {
        loadingText: 'Loading...',
        loadingType: 'border'
    };

    $.fn.GUIButton = function(option){
        return $.each(this, function(){
            let $this   = $(this);
            let data    = $this.data('gui.button');
            let options = typeof option == 'object' && option;

            if(!data) $this.data('gui.button', (data = new GUIButton(this, options)));
            if(option) data.setState(option);
        });
    };

}(jQuery);