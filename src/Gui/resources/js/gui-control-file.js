!function($){
    'use strict';

    let GUIControlFile = function(element, options){
        let $this = this;

        this.options = $.extend({}, GUIControlFile.DEFAULTS, options);
        this.$element = $(element);
        this.$input = $('input[type=text]', this.$element.next());
        this.$button = this.$input.prev();

        if(this.$element.prop('disabled') || this.$element.prop('readonly')){
            this.$button.addClass('disabled');
        }

        this.$button.on('click', function(){
            $this.$element.trigger('click');
        });

        this.$element.on('change', function(){
            $this.updateDisplay();
        });

        this.$input.val(this.options.filenameDisplay ? this.options.filenameDisplay : this.options.strings.empty);

        $('.control-label label', this.$element.parents('form-group')).on('click', function(e){
            e.preventDefault();
            $this.$input.trigger('focus');
        });
    };

    GUIControlFile.prototype.updateDisplay = function(){
        let f = [], c;

        for(let i = 0; i < this.$element[0].files.length; i++){
            f.push(this.$element[0].files[i].name);
        }

        if(f.length > 1){
            c = this.options.strings.multiple.replace('%d', f.length.toString());
        } else if(f.length === 1){
            c = f[0];
        } else {
            c = this.options.strings.empty;
        }

        this.$input.val(c);
    };

    GUIControlFile.DEFAULTS = {
        filenameDisplay: "",
        strings: { multiple: "%d files selected", empty: "No file chosen" }
    };

    $.fn.GUIControlFile = function(option){
        $.each(this, function(){
            let $this = $(this);
            let data = $this.data('gui.file');
            let options = typeof option == 'object' && option;

            if(!data) $this.data('gui.file', new GUIControlFile(this, options));
        });
    };

}(jQuery);