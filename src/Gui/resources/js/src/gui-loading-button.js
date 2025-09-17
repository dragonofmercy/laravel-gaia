import $ from 'jquery'

export class GuiLoadingButton {
    constructor(element){
        const loadingContent = $('[data-loading-content]', element);
        loadingContent.remove();

        this.$element = $(element);
        this.data = {
            loadingText: loadingContent.html(),
            resetText: this.$element.html()
        };
    }

    reset(){
        this.state('reset');
    }

    state(state){
        state += 'Text'
        setTimeout(() => {
            this.$element.html(this.data[state]);
            if(state == 'loadingText'){
                this.$element.addClass('loading-active');
            } else if(this._isLoading()) {
                this.$element.removeClass('loading-active');
            }
        });
    }

    _isLoading(){
        return this.$element.hasClass('loading-active');
    }
}

$.fn.GuiLoadingButton = function(option){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.button');
        const options = typeof option == 'object' && option;
        if(!data) $this.data('gui.button', new GuiLoadingButton(this, options));
        if(option) $this.data('gui.button').state(option);
    });
};

export default GuiLoadingButton;