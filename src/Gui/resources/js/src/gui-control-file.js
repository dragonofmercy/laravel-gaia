import $ from 'jquery';

export class GuiControlFile {

    static DEFAULTS = {
        stringSelectedFiles: '%d files selected'
    }

    constructor(element, options){
        this.options = $.extend({}, GuiControlFile.DEFAULTS, options);
        this.$element = $(element);
        this.$input = this.$element.next().find('input[type=text]');
        this.$btnBrowse = this.$element.next().find('[data-trigger=browse]');

        this.$btnBrowse.on('click', e => {
            $(e.currentTarget).data('bs-tooltip').hide();
            this.$element.trigger('click');
        });

        this.$element.on('change', () => {
            this.update();
        });
    }

    update(){
        const rowElement = this.$element[0];
        const files = Array.from(rowElement.files, file => file.name);
        const val = files.length > 1
            ? this.options.stringSelectedFiles.replace('%d', files.length.toString())
            : files[0] || '';

        this.$input.val(val);
    }
}

$.fn.GuiControlFile = function(option){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.control');
        const options = typeof option == 'object' && option;
        if(!data) $this.data('gui.control', new GuiControlFile(this, options));
    });
};

export default GuiControlFile;