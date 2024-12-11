!function($){
    'use strict';

    let GUIControlMulti = function(element, options){
        let $self = this;

        this.$element = $(element);
        this.options = $.extend({}, GUIControlMulti.DEFAULTS, options);
        this.eventNS = "." + gui.getUID('multiline');

        $('a[data-trigger=add]', this.$element).on('click', function(){
            $self.add();
        });

        this.count = $('table>tbody>tr', this.$element).length;

        $('table>tbody>tr', this.$element).each(function(){
            $self.updateEvents($(this));
        });

        this.updateRestrictions();

        if(this.options.sortable){
            new Sortable($('table>tbody', this.$element)[0], {
                handle: 'a[data-trigger=move]',
                direction: 'vertical',
                ghostClass: "sortable-ghost",
                animation: 100,
                onChange: function(){
                    $self.bindNewLineEvent();
                }
            });
        }

        $self.statusChanged();

        this.$element.trigger('gui.loaded');
    };

    GUIControlMulti.prototype.updateEvents = function($line){
        let $self = this;

        $('a[data-trigger=remove]', $line).on('click' + this.eventNS, function(){
            $self.remove($(this).parents('tr'));
        });
    };

    GUIControlMulti.prototype.bindNewLineEvent = function(){
        let $self = this;

        if(this.options.newLineOnTab){
            $('input', this.$element).off('keydown' + this.eventNS);
            $('table>tbody>tr:last-child>td input', this.$element).last().on('keydown' + this.eventNS, function(e){
                if(e.keyCode == 9){
                    $self.add();
                }
            });
        }
    };

    GUIControlMulti.prototype.add = function(force){
        force = force || false;

        if(this.count < this.options.max || this.options.max < 1 || force){
            let $last = $('table>tbody>tr:last-child', this.$element)
            let $template = $last.clone();

            $('input, select, textarea', $template).removeClass('is-invalid').val('');
            $('.invalid-feedback', $template).remove();
            $template.insertAfter($last);

            this.updateEvents($template);
            this.count++;
            this.updateRestrictions();
            this.$element.trigger('gui.add', $template);
        }
    };

    GUIControlMulti.prototype.remove = function($line){
        if(this.count === 1){
            this.add(true);
        }

        $line.remove();

        this.count--;
        this.updateRestrictions();
        this.$element.trigger('gui.remove');
    };

    GUIControlMulti.prototype.updateRestrictions = function(){
        let $btnAdd = $('a[data-trigger=add]', this.$element);
        let $btnMove = $('a[data-trigger=move]', this.$element);

        if(this.options.max > 0 && this.count >= this.options.max){
            $btnAdd.addClass('disabled');
        } else {
            $btnAdd.removeClass('disabled');
        }

        if(this.count === 1){
            $btnMove.addClass('disabled');
        } else {
            $btnMove.removeClass('disabled');
        }

        this.bindNewLineEvent();
    };

    GUIControlMulti.prototype.statusChanged = function(){
        if(this.$element.hasClass('readonly')){
            $('a', this.$element).addClass('disabled');
            $('input, select, textarea', this.$element).prop('readonly', true);
        } else {
            $('a', this.$element).removeClass('disabled');
            $('input, select, textarea', this.$element).prop('readonly', false);
            this.updateRestrictions();
        }

        this.bindNewLineEvent();
    };

    GUIControlMulti.DEFAULTS = {
        sortable: true,
        max: 5,
        newLineOnTab: true
    };

    $.fn.GUIControlMulti = function(option){
        $.each(this, function(){
            let $self   = $(this);
            let data    = $self.data('gui.multi');
            let options = typeof option == 'object' && option;

            if(!data) $self.data('gui.multi', new GUIControlMulti(this, options));
        });
    };

}(jQuery);