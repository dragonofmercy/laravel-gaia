!function($){
    'use strict';

    let GUIControlIpv4 = function(element){
        let $this = this;

        this.$element  = $(element);

        $('.control-label label', this.$element.parents('form-group')).on('click', function(){
            $('input', $this.$element).first().trigger('select');
        });

        $('input', $this.$element).each(function(){
            let current = $(this);

            if($this.$element.attr('disabled') || $this.$element.attr('readonly')){
                $(this).prop('readonly', true);
            }

            $(this).on('keypress', function(e){
                $this.keypress(e);
            }).on('keyup', function(e){
                $this.keyup(e);
            }).on('keydown', function(e){
                $this.keydown(e);
            }).on('blur', function(){
                $this.validate(current);
            }).on('paste', function(e){
                if($this.$element.attr('disabled') || $this.$element.attr('readonly')){
                    return false;
                }
                let pattern = new RegExp(/^([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/, 'ig');
                let content = e.originalEvent.clipboardData.getData('text');
                if(pattern.test(content)){
                    e.preventDefault();
                    let parts = content.split('.');
                    for(let i = 0; i < parts.length; i++){
                        $('input', $this.$element).eq(i).val(parts[i]).trigger('focus').trigger('blur');
                    }
                }
            });
        });
    };

    GUIControlIpv4.prototype.keypress = function(e){
        if(e.keyCode == 46){
            e.preventDefault();
            let $target = $(e.target);
            if(this.getInputPosition($target) < 3){
                $target.next().next().trigger('focus').trigger('select');
            }
        } else if(e.keyCode > 31 && (e.keyCode < 48 || e.keyCode > 57)){
            e.preventDefault();
        }
    };

    GUIControlIpv4.prototype.keyup = function(e){
        let $target = $(e.target);
        if(e.which > 30 && e.which !== 46 && !(e.which >= 35 && e.which <= 40) && (this.getInputPosition($target) < 3 && $target.val().length == 3)){
            $target.next().next().trigger('focus').trigger('select');
        }
    };

    GUIControlIpv4.prototype.keydown = function(e){
        let $target = $(e.target);
        let valueLength = $target.val().length;
        let position = this.getInputPosition($target);

        if(e.which === 8 && valueLength === 0 && position > 0){
            e.preventDefault();
            $target.prev().prev().trigger('focus').trigger('select');
        } else if(e.which === 46 && valueLength === 0 && position < 3){
            e.preventDefault();
            $target.next().next().trigger('focus').trigger('select');
        } else if((e.which === 37 || e.which === 39)){
            // Add left and right arrow key move beetween fields
            let selectionStart = e.target.selectionStart;
            let selectionEnd = e.target.selectionEnd;

            if(selectionStart === selectionEnd){
                if(e.which === 37 && position > 0 && selectionStart === 0){
                    // Left arrow key
                    e.preventDefault();
                    $target.prev().prev().trigger('focus').trigger('select');
                } else if(e.which === 39 && selectionEnd === valueLength){
                    // Right arrow key
                    e.preventDefault();
                    $target.next().next().trigger('focus').trigger('select');
                }
            }
        }
    };

    GUIControlIpv4.prototype.getInputPosition = function($input){
        let index = $input.attr('name').match(/\[([0-9])]$/i);
        return index !== null ? index[1] : 0;
    };

    GUIControlIpv4.prototype.validate = function($input){
        let v = $input.val();

        if(parseInt(v) < 0 || parseInt(v) > 255){
            if(parseInt(v) < 0){
                v = 0;
            } else if(parseInt(v) > 255) {
                v = 255;
            }
        }

        $input.val(v);
    };

    $.fn.GUIControlIpv4 = function(){
        $.each(this, function(){
            let $this = $(this);
            let data = $this.data('gui.ipv4');
            if(!data) $this.data('gui.ipv4', new GUIControlIpv4(this));
        });
    };

}(jQuery);