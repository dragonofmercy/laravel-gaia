import $ from 'jquery';

export class GuiFormRemote {

    constructor(element){
        this.$element = $(element);
        this.$element.on('submit', e => {
            e.preventDefault();
            this.submit();
        })
    }

    submit(){
        const $target = $(this.$element.attr('data-target'));
        $.ajax({
            url: this.$element.attr('action'),
            type: this.$element.attr('method'),
            data: this.$element.serialize(),
            success: (data) => {
                $target.html(data);
            },
            error: (xhr) => {
                gui.printError(xhr.responseText, $target)
            },
        })
    }
}

$.fn.GuiFormRemote = function(){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.form-remote');
        if(!data) $this.data('gui.form-remote', new GuiFormRemote(this));
    });
};