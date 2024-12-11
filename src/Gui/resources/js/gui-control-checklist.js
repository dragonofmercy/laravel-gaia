!function($){
    'use strict';

    let GUIControlChecklist = function(element){
        let $this = this;

        this.$element = $(element);
        this.shift = false;
        this.lastIndex = null;
        this.$map = $('.form-check', $this.$element);

        $(document).on('keydown', function(e){
            if(e.key === 'Shift'){
                $this.shift = true;
            }
        }).on('keyup', function(e){
            if(e.key === 'Shift'){
                $this.shift = false;
            }
        });

        this.$map.on('click', function(e){
            if($(e.target).is('input') && $('input[type=checkbox]', $(this)).not(':disabled').not('[readonly]').length){
                if($this.shift && $this.lastIndex !== null){
                    $this.iterate($this.$map.index(this));
                }
                $this.lastIndex = $this.$map.index(this);
            }
        });

        $('.checklist-heading a', this.$element).on('click', function(e){
            e.preventDefault();
            $this.$map.each(function(){
                $this.check($(this), $(e.target).data('trigger') === 'select');
            });
        });
    };

    GUIControlChecklist.prototype.iterate = function(index){
        let iteratorIndex;

        if(this.lastIndex <= index){
            for(iteratorIndex = this.lastIndex; iteratorIndex <= index; iteratorIndex++){
                this.check(this.$map[iteratorIndex], true);
            }
        } else if(this.lastIndex >= index){
            for(iteratorIndex = this.lastIndex; iteratorIndex >= index; iteratorIndex--){
                this.check(this.$map[iteratorIndex], true);
            }
        }
    };

    GUIControlChecklist.prototype.check = function($checkbox, status){
        status = status || false;

        let input = $('input[type=checkbox]', $checkbox).not(':disabled').not('[readonly]');

        if(input.length > 0){
            input.prop('checked', status).trigger('change');
        }
    };

    $.fn.GUIControlChecklist = function(){
        $.each(this, function(){
            let $this = $(this);
            let data = $this.data('gui.checklist');
            if(!data) $this.data('gui.checklist', new GUIControlChecklist(this));
        });
    };

}(jQuery);