import $ from 'jquery';
import { Tooltip } from 'bootstrap';

export class GuiCopy {

    static DEFAULTS = {
        copyContent: null,
        copyTarget: null,
        copyTooltip: true,
        copyTooltipTarget: null,
        copyTooltipPlacement: 'top',
        copyTooltipTitle: 'Copied!',
        copyHtml: false
    }

    constructor(element, options){
        this.options = $.extend({}, GuiCopy.DEFAULTS, $.extend({}, $(element).data(), options));
        this.$element = $(element);
        this.options.copyTarget = null === this.options.copyTarget ? this.$element : $(this.options.copyTarget);
        this.options.copyTooltipTarget = null === this.options.copyTooltipTarget ? this.options.copyTarget : $(this.options.copyTooltipTarget);

        this.$element.on('click', e => {
            e.preventDefault();
            this.copy();
        });
    }

    copy(){
        if(!navigator.clipboard){
            console.error('Clipboard API not supported');
            return;
        }

        navigator.clipboard.writeText(this._getContent()).then(() => {
            if(!this.options.copyTooltip){
                return;
            }

            const defaultTooltip = Tooltip.getInstance(this.options.copyTooltipTarget[0]);
            const defaultConfig = defaultTooltip?._getConfig() || {};

            if(defaultTooltip){
                defaultTooltip.dispose();
            }

            const copyTooltipInstance = Tooltip.getOrCreateInstance(this.options.copyTooltipTarget[0], {
                title: this.options.copyTooltipTitle,
                trigger: 'manual',
                placement: this.options.copyTooltipPlacement
            });

            copyTooltipInstance.show();

            setTimeout(() => {
                copyTooltipInstance.dispose();
                if(defaultTooltip){
                    Tooltip.getOrCreateInstance(this.options.copyTooltipTarget[0], defaultConfig);
                }
            }, 500);
        });
    }

    _getContent(){
        if(this.options.copyTarget.is('input')){
            return this.options.copyTarget.val();
        } else if(this.options.copyHtml){
            return this.options.copyTarget.html();
        } else {
            if(this.options.copyContent){
                return this.options.copyContent;
            }
            return this.options.copyTarget.text();
        }
    }
}

$.fn.GuiCopy = function(option){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.copy');
        const options = typeof option == 'object' && option;
        if(!data) $this.data('gui.copy', new GuiCopy(this, options));
    });
};

export default GuiCopy;