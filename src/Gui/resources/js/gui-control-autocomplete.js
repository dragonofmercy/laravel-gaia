!function($){
    'use strict';

    let GUIControlAutocomplete = function(element, options){
        let $this = this;

        this.$element = $(element);
        this.options = $.extend({}, GUIControlAutocomplete.DEFAULTS, options);
        this.id = gui.getUID('ac');
        this.lastSearch = null;
        this.dropdown = null;

        this.$element.on('keydown', function(e){
            $this.keydown(e);
        }).on('keyup', function(e){
            $this.keyup(e);
        });

        if(this.$element.prev().is('input[type=hidden]')){
            $('.control-label label', this.$element.parents('form-group')).on('click', function(){
                $this.$element.trigger('focus');
            });
        }
    };

    GUIControlAutocomplete.prototype.keydown = function(e){
        switch(e.keyCode){
            case GUIControlAutocomplete.KEYS.ARROW_DOWN:
            case GUIControlAutocomplete.KEYS.PAGE_DOWN:
                e.preventDefault();
                this.move(1);
                break;

            case GUIControlAutocomplete.KEYS.ARROW_UP:
            case GUIControlAutocomplete.KEYS.PAGE_UP:
                e.preventDefault();
                this.move(-1);
                break;

            case GUIControlAutocomplete.KEYS.TAB:
            case GUIControlAutocomplete.KEYS.ENTER:
                if(this.dropdown && $('a.active', this.dropdown).length){
                    e.preventDefault();
                    this.validate($('a.active', this.dropdown));
                } else if(this.dropdown){
                    this.removeDropdown();
                }
                break;
        }
    };

    GUIControlAutocomplete.prototype.keyup = function(e){
        switch(e.keyCode){
            case GUIControlAutocomplete.KEYS.ESCAPE:
                if(this.dropdown){
                    e.preventDefault();
                    this.removeDropdown();
                }
                break;

            case GUIControlAutocomplete.KEYS.TAB:
            case GUIControlAutocomplete.KEYS.ENTER:
                if(this.dropdown){
                    e.preventDefault();
                }
                break;
            default:
                if(this.$element.val().length >= this.options.minLength && this.lastSearch !== this.$element.val()){
                    this.search();
                    this.lastSearch = this.$element.val();
                } else if(this.$element.val().length < 1 && this.dropdown){
                    this.removeDropdown();
                }
                break;
        }
    };

    GUIControlAutocomplete.prototype.removeDropdown = function(){
        if(this.dropdown){
            this.dropdown.remove();
            this.lastSearch = null;
            this.dropdown = null;

            $(document).off('mousedown.gui.autocomplete touchend.gui.autocomplete');
            $(window).off('resize.gui.autocomplete scroll.gui.autocomplete');
        }
    };

    GUIControlAutocomplete.prototype.search = function(){
        let $this = this;

        if(this.options.url !== null){
            if(this.xhr !== undefined){
                this.xhr.abort();
            }

            this.xhr = $.ajax({
                url: this.options.url,
                headers: {"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr('content')},
                data: { term: this.$element.val(), limit: this.options.limit },
                cache: this.options.cache,
                success: function(data){
                    $this.processData(data);
                }
            });
        }
    };

    GUIControlAutocomplete.prototype.processData = function(data){
        if(typeof data !== 'object'){
            data = JSON.parse();
        }

        if(data.length){
            this.fill(data);
        } else {
            this.removeDropdown();
        }
    };

    GUIControlAutocomplete.prototype.fill = function(items){
        if(!$('#' + this.id).length){
            $('body').append($('<div id="' + this.id + '" class="dropdown-menu autocomplete" />'));

            $(window).on('resize.gui.autocomplete scroll.gui.autocomplete', function(){
                $this.updatePosition();
            });

            $(document).on('mousedown.gui.autocomplete touchend.gui.autocomplete', function(e){
                if(!$(e.target).closest('.dropdown-menu.autocomplete').length) {
                    $this.removeDropdown();
                }
            });
        }

        let $this = this;
        let itemCount = 0;

        this.dropdown = $('#' + this.id).empty();

        $.each(items, function(_, v){
            if(itemCount <= $this.options.limit){
                let $item = $('<a class="dropdown-item" />');

                if(v[$this.options.textField] !== undefined){
                    $item.html(v[$this.options.textField]);
                } else {
                   $item.html(JSON.stringify(v));
                }

                $item.attr('data-value', v[$this.options.valueField] ?? $this.escape($item));
                $item.on('click', function(e){
                     e.preventDefault();
                     $this.validate($item);
                });

                $this.dropdown.append($item);
                itemCount++;
            }
        });

        this.updatePosition();
    };

    GUIControlAutocomplete.prototype.escape = function($item){
        if($item.find('[data-text]').length){
            return $item.find('[data-text]')[0].innerText;
        } else {
            return $item[0].innerText;
        }
    };

    GUIControlAutocomplete.prototype.updatePosition = function(){
        let $selector = this.$element.attr('data-reference') == "parent" ? this.$element.parent() : this.$element;
        let position = $selector.offset();
        position.top += $selector.outerHeight();
        this.dropdown.css({top: position.top, left: position.left, minWidth: $selector.outerWidth()});
    };

    GUIControlAutocomplete.prototype.move = function(direction){
        if(this.dropdown){
            if($('a.active', this.dropdown).length > 0){
                let activeItem = $('a.active', this.dropdown);
                if(activeItem.is('a:' + (direction < 0 ? 'first-child' : 'last-child'))){
                    activeItem.removeClass('active');
                    $('a:' + (direction < 0 ? 'last-child' : 'first-child'), this.dropdown).addClass('active');
                } else {
                    activeItem.removeClass('active');
                    if(direction > 0){
                        activeItem.next().addClass('active');
                    } else {
                        activeItem.prev().addClass('active');
                    }
                }
            } else {
                $('a:first-child', this.dropdown).addClass('active');
            }
        }
    };

    GUIControlAutocomplete.prototype.validate = function($item){
        let text = this.escape($item);
        let eventData = [$item.data('value'), text];

        this.$element.val(text);
        this.removeDropdown();
        this.$element.trigger('gui.validate', eventData);
    };

    GUIControlAutocomplete.KEYS = {
        ARROW_UP: 38,
        ARROW_DOWN: 40,
        PAGE_UP: 33,
        PAGE_DOWN: 34,
        ESCAPE: 27,
        ENTER: 13,
        TAB: 9
    };

    GUIControlAutocomplete.DEFAULTS = {
        url: null,
        cache: false,
        minLength: 1,
        limit: 10,
        valueField: 'value',
        textField: 'text'
    };

    $.fn.GUIControlAutocomplete = function(option){
        $.each(this, function(){
            let $this = $(this);
            let data = $this.data('gui.autocomplete');
            let options = typeof option == 'object' && option;

            if(!data) $this.data('gui.autocomplete', new GUIControlAutocomplete(this, options));
        });
    };

}(jQuery);