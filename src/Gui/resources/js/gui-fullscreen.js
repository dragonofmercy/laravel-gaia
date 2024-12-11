!function($){
    'use strict';

    let GUIFullscreen = function(element, options){
        let $this = this;

        this.$element = $(element);
        this.options = $.extend({}, GUIFullscreen.DEFAULTS, $.extend({}, this.$element.data(), options));

        this.$element.on('click', function(e){
            e.preventDefault();
            $this.goFullscreen();
        });
    };

    GUIFullscreen.prototype.goFullscreen = function(){
        if(document.fullscreenEnabled){
            let $target = $(this.options.target);

            if(document.fullscreenElement !== null){
                document.exitFullscreen();
            } else {
                $target[0].requestFullscreen();
            }
        } else {
            alert("Fullscreen is not enabled on your browser");
        }
    };

    GUIFullscreen.DEFAULTS = {
        target: 'html'
    };

    $.fn.GUIFullscreen = function(option){
        $.each(this, function(){
            let $this = $(this);
            let data = $this.data('gui.fullscreen');
            let options = typeof option == 'object' && option;

            if(!data) $this.data('gui.fullscreen', new GUIFullscreen(this, options));
        });
    };

}(jQuery);