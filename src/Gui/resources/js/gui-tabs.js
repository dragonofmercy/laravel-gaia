!function($){
    'use strict';

    let GUITabs = function(element, options){
        let $this = this;

        this.$element = $(element);
        this.options = $.extend({}, GUITabs.DEFAULTS, $.extend({}, this.$element.data(), options));
        this.id = this.$element.attr('id') || false;
        this.hasError = false;

        // Add click events on tabs
        $('.nav-item>a', this.$element).on('click', function(e){
            $this.show($(this).data('target'), e);
        });

        this.displayTabInError();

        if(!this.hasError && this.options.tabsRemember && this.id) this.switchFromUrl();

        if($('.nav-item>a.active', this.$element).length == 0){
            this.show($('.nav-item>a[data-target]:first-child', this.$element).data('target'));
        }

        if(this.id){
            $(window).on('hashchange.' + this.id, function(){
                $this.switchFromUrl(true);
            });
        }
    };

    GUITabs.prototype.displayTabInError = function(){
        $('.nav-item>a[data-target]', this.$element).each(function(){
            let target = $(this).data('target');
            if($('[data-pane="' + target + '"] .has-error').length){
                $('[data-target="' + target + '"]').addClass('tab-error');
            }
        });

        if($('.nav-item>a.tab-error', this.$element).length > 0){
            this.hasError = true;
            this.show($('.nav-item>a.tab-error', this.$element).first().data('target'));
        } else {
            this.hasError = false;
        }
    };

    GUITabs.prototype.switchFromUrl = function(show_def){
        show_def = show_def || false;

        let json = this.getJsonContent();
        if(json[this.id] !== undefined){
            $('[data-target="' + json[this.id] + '"]').trigger('click');
        } else if(show_def){
            if(this.hasError){
                this.displayTabInError();
            } else {
                this.show($('.nav-item>a[data-target]:first-child', this.$element).data('target'));
            }
        }
    };

    GUITabs.prototype.saveToUrl = function(index){
        let json = this.getJsonContent();
        json[this.id] = index;
        location.hash = btoa(JSON.stringify(json));
    };

    GUITabs.prototype.getJsonContent = function(){
        if(location.hash){
            try{ return JSON.parse(atob(location.hash.substring(1))); }catch(e){}
        }
        return {};
    };

    GUITabs.prototype.show = function(index, e){
        $('[data-target="' + index + '"]').parent().parent().find('.nav-item>a.active').removeClass('active');
        $('[data-pane="' + index + '"]').parent().find('.tab-pane.active').removeClass('active');
        $('[data-target="' + index + '"],[data-pane=' + index + ']').addClass('active');
        $('[data-pane="' + index + '"]').trigger('gui.tab.shown');

        // Save only when tab clicked
        if((e !== undefined || this.hasError) && this.options.tabsRemember && this.id) this.saveToUrl(index);
    };

    GUITabs.DEFAULTS = {
        tabsRemember: true
    };

    $.fn.GUITabs = function(option){
        return $.each(this, function(){
            let $this   = $(this);
            let data    = $this.data('gui.tabs');
            let options = typeof option == 'object' && option;

            if(!data) $this.data('gui.tabs', new GUITabs(this, options));
        });
    };

}(jQuery);