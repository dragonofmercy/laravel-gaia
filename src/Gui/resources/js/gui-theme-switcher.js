!function($){
    'use strict';

    let GUIThemeSwitcher = function(element){
        let $this = this;

        $(element).on('click', function(e){
            e.preventDefault();
            $this.requestChange($(this).attr('data-theme'));
        });
    };

    GUIThemeSwitcher.prototype.requestChange = function(type){
        Cookies.set('dark-mode', (type === 'dark').toString(), { expires: 365 });
        location.reload();
    };

    $.fn.GUIThemeSwitcher = function(){
        $.each(this, function(){
            new GUIThemeSwitcher(this);
        });
    };

}(jQuery);