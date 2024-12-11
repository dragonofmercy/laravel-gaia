!function($){
    'use strict';

    let KEY = {
        BACKSPACE: 8,
        TAB: 9,
        ENTER: 13,
        ESCAPE: 27,
        UP: 38,
        DOWN: 40,
        CTRL: 17,
        SHIFT: 16
    };

    let GUIControlTokenize = function(element, options){
        let self = this;
        let settings = $.extend({}, GUIControlTokenize.DEFAULTS, options);

        $.extend(self, {
            $select: $(element),
            $input: $('<input type="text" autocomplete="new-password" />'),
            $document: $(document),
            $window: $(window),
            $testInput: null,
            $activeOption: null,
            settings: settings,
            eventNS: "." + gui.getUID('tokenize'),
            isOpen: false,
            lastValue: '',
            isShiftDown: false,
            isDisabled: false,
            isInputDetach: false,
            xhr: null,
            sifter: new Sifter({}, { diacritics: true })
        });

        self.isDisabled = self.$select.attr('readonly') || self.$select.attr('disabled');
        self.setupTemplates();

        self.$container = $('.tokenize-container', self.$select.parent());
        self.$dropdown = $('<div class="dropdown-menu autocomplete" />');
        self.$input.on({
            mousedown: function(e){ return self.onMouseDown(e) },
            keydown: function(e){ return self.onKeyDown(e) },
            keypress: function(e){ return self.onKeyPress(e) },
            input: function(){ return self.onInput() },
            focus: function(){ return self.onFocus() },
            blur: function(){ return self.onBlur() }
        }).appendTo(self.$container);

        self.autoGrow(self.$input);

        self.$dropdown.on('mousedown touchstart', '[data-selectable]', function(e){ return self.onOptionSelect(e) });
        self.$container.on('focusout', function(){ self.resetPending() });
        self.$container.on('mousedown touchstart', '.token>a', function(e){ if(e.button < 1) self.removeItem($(this).parent().attr('data-value')) });
        self.$document.on(['keydown' + self.eventNS, 'keyup' + self.eventNS].join(' '), function(e){
            if(e.type === 'keydown'){
                self.isShiftDown = e.shiftKey;
            } else {
                if(e.which === KEY.SHIFT) self.isShiftDown = false;
            }
        });

        if(self.settings.layoutDirection === 'column'){
            self.$container.addClass('layout-column');
        } else {
            self.$container.addClass('layout-row');
        }

        if(self.$select.attr('multiple')){
            $('option:selected', self.$select).each(function(){
                self.insertAtCaret(self.render('item', { value: $(this).attr('value'), text: $(this).html() }));
            });
        } else {
            if(self.$select[0].selectedIndex > 0){
                let option = $('option:selected', self.$select).first()
                self.insertAtCaret(self.render('item', { value: option.attr('value'), text: option.html() }));
            }
        }

        if(settings.sortable && !self.isDisabled){
            self.$container.addClass('sortable');

            new Sortable(self.$container[0], {
                handle: '.token>span',
                draggable: '.token',
                animation: 100,
                forceFallback: true,
                onSort: function(){
                    self.updateSelect();
                }
            });
        }

        if(self.$select.attr('data-placeholder')){
            self.$input.attr('placeholder', self.$select.attr('data-placeholder'));
            self.$input.trigger('change');
        }

        self.setTextboxVisibility(!self.isFull());

        if(self.isDisabled){
            self.$container.addClass('disabled');
            self.setTextboxVisibility(false);
        }
    };

    GUIControlTokenize.prototype.setupTemplates = function(){
        let settings = this.settings;
        let field_label = 'text';
        let templates = {
            'option': function(data, escape){
                return '<div class="dropdown-item">' + escape(data[field_label]) + '</div>';
            },
            'item': function(data, escape){
                return '<div class="token"><span>' + escape(data[field_label]) + '</span><div class="vr"></div><a><i class="fa-solid fa-xmark"></i></a></div>';
            }
        }

        settings.render = $.extend({}, templates, settings.render);
    };

    GUIControlTokenize.prototype.isFull = function(){
        if(this.settings.maxItems === null){
            return false;
        }

        if(this.$select.attr('multiple')){
            return $('option:selected', this.$select).length >= this.settings.maxItems
        } else {
            return this.$select[0].selectedIndex > 0;
        }
    };

    GUIControlTokenize.prototype.setTextboxValue = function(value){
        this.$input.val(value).trigger('change');
    };

    GUIControlTokenize.prototype.setTextboxVisibility = function(visible){
        let self = this;

        if(visible){
            if(self.isInputDetach){
                self.$input.appendTo(self.$container);
                self.isInputDetach = false;
            }
        } else {
            self.$input.detach();
            self.isInputDetach = true;
        }
    };

    GUIControlTokenize.prototype.dropdownOpen = function(){
        let self = this;
        self.$dropdown.appendTo('body');
        self.isOpen = true;

        self.$window.on(['scroll' + self.eventNS, 'resize' + self.eventNS].join(' '), function(){
            if(self.isOpen){
                self.updatePosition();
            }
        });

        self.$document.on(['mousedown' + self.eventNS, 'touchend' + self.eventNS].join(' '), function(e){
            if(!$(e.target).closest(self.$container).length && !$(e.target).closest(self.$dropdown).length && self.isOpen){
                self.dropdownRemove();
            }
        });

        self.updatePosition();
    };

    GUIControlTokenize.prototype.dropdownRemove = function(){
        let self = this;

        self.$document.off(['mousedown' + self.eventNS, 'touchend' + self.eventNS].join(' '));
        self.$window.off(['scroll' + self.eventNS, 'resize' + self.eventNS].join(' '));

        self.$dropdown.detach();
        self.isOpen = false;
        self.$activeOption = null;
    };

    GUIControlTokenize.prototype.updatePosition = function(){
        let self = this;
        let position = self.$container.offset();
        position.top += self.$container.outerHeight();
        self.$dropdown.css({top: position.top, left: position.left}).outerWidth(self.$container.outerWidth());
    };

    GUIControlTokenize.prototype.search = function(){
        let i, score, options = [], results = [], self = this;
        let settings = self.settings;
        let query = (self.$input.val()).trim().normalize("NFD").replace(/[\u0300-\u036f]/g, "");

        if(self.settings.provider !== 'select'){
            self.searchRemote(query);
        } else {
            $('option', self.$select).not(':selected, :disabled').each(function(){
                options.push({ value:$(this).attr('value').trim(), text:$(this).html().trim() });
            });

            self.sifter.items = options;
            score = self.sifter.search(query, {
                fields: 'text',
                conjunction: settings.searchConjunction,
                respect_word_boundaries: settings.searchRespectWordBoundaries
            });

            if(score.items.length > 0){
                for(i = 0; i < score.items.length; i++){
                    results.push(options[score.items[i]['id']]);
                }
            }

            self.parseResults(results);
        }
    };

    GUIControlTokenize.prototype.searchRemote = function(query){
        let self = this, results = [];

        if(self.xhr !== undefined && self.xhr !== null){
            self.xhr.abort();
        }

        self.xhr = $.ajax(self.settings.provider, {
            data: { [self.settings.providerQueryParameter]: query },
            headers: {"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr('content')},
            cache: self.settings.providerCache,
            dataType: 'json',
            success: function(data){
                if(data.length){
                    $.each(data, function(_, v){
                        let item = { value: "", text: ""};
                        let value = v[self.settings.valueField];

                        if($('option[value="' + self.escapeQuery(value) + '"]', self.$select).length == 0){
                            item.value = value;

                            if(v[self.settings.labelField] !== undefined){
                                item.text = v[self.settings.labelField];
                            } else {
                                item.text = v[self.settings.valueField];
                            }

                            results.push(item);
                        }
                    });
                }
                self.parseResults(results);
            }
        });
    };

    GUIControlTokenize.prototype.refreshOptions = function(tiggerDropdown){
        let results = [], self = this;
        let query = (self.$input.val()).trim();

        tiggerDropdown = tiggerDropdown ?? false;

        if(query !== '' || tiggerDropdown){
            self.search(query);
        } else {
            self.parseResults(results);
        }
    };

    GUIControlTokenize.prototype.parseResults = function(results){
        let n, i, html, self = this;

        n = results.length;

        if(n > 0 && !self.isOpen) {
            self.dropdownOpen();
        } else if(n < 1 && self.isOpen) {
            self.dropdownRemove();
            return;
        }

        if(typeof self.settings.maxOptions === 'number'){
            n = Math.min(n, self.settings.maxOptions);
        }

        html = document.createDocumentFragment();

        for(i = 0; i < n; i++){
            html.appendChild(self.render('option', results[i]));
        }

        self.$dropdown.html(html);

        if(self.settings.setFirstOptionActive){
            self.setActiveOption(self.$dropdown.find('[data-selectable]:first'));
        }
    };

    GUIControlTokenize.prototype.render = function(template, data){
        let $html, value, self = this;
        let settings = self.settings;

        if(template == 'option' || template == 'item'){
            value = self.escapeHtml(data['value']);
        }

        $html = $(settings.render[template].apply(this, [data, self.escapeHtml]));

        if(template == 'item' && self.isDisabled){
            $html.find('.vr, a').remove();
        }

        if(template == 'option' || template == 'item'){
            $html.attr('data-value', value);
        }

        if(template == 'option'){
            $html.attr('data-selectable', '');
        }

        return $html[0];
    };

    GUIControlTokenize.prototype.setActiveOption = function($option){
        let self = this;

        if(self.$activeOption !== null){
            self.$activeOption.removeClass('active');
        }

        self.$activeOption = $option;
        self.$activeOption.addClass('active');
    };

    GUIControlTokenize.prototype.moveOptionsSelection = function(direction){
        let self = this, index = 0;
        let $options = self.$dropdown.find('[data-selectable]');

        if(self.$activeOption !== null){
            index = $options.index(self.$activeOption) + 1
        }

        index+= direction;

        if(index < 1){
            index = $options.length;
        } else if(index > $options.length){
            index = 1;
        }

        self.setActiveOption($options.eq(index - 1));
    };

    GUIControlTokenize.prototype.onOptionSelect = function(e){
        let $target, self = this;

        if(e.preventDefault){
            e.preventDefault();
            e.stopPropagation();
        }

        $target = $(e.currentTarget);
        self.setActiveOption($target);

        // prevent right click on option
        if(e.button && e.button === 2){
            return;
        }

        self.createItem($target.attr('data-value'), $target.html());
    };

    GUIControlTokenize.prototype.createItem = function(value, label){
        let self = this;

        if(self.settings.provider !== 'select'){
            let $option = $('<option />').attr('value', self.escapeHtml(value)).html(label).attr('data-remote', 1);
            self.$select.prepend($option);
        }

        if(typeof value !== 'undefined'){
            self.lastValue = null;
            self.selectItem(value);
            self.setTextboxValue('');
            self.dropdownRemove();
        }
    };

    GUIControlTokenize.prototype.selectItem = function(value){
        let $option, self = this;

        $option = $('[value="' + self.escapeQuery(value) + '"]', self.$select);

        if($option.length > 0){
            $option.attr('selected', 'selected');
            self.insertAtCaret(self.render('item', { value: value, text: $option.html() }));
            self.$select.trigger('tokenize_item_select', [value]);
            self.setTextboxVisibility(!self.isFull());
            self.setInputFocus();
        }
    };

    GUIControlTokenize.prototype.removeItem = function(value){
        let self = this;
        let $item = $('.token[data-value="' + self.escapeQuery(value) + '"]', self.$container);
        let $option = $('option[value="' + self.escapeQuery(value) + '"]', self.$select);

        if($item.length > 0){
            $item.remove();
        }

        if($option.length > 0){
            $option.removeAttr('selected');
            if($option.attr('data-remote')){
                $option.remove();
            }
        }

        self.$select.trigger('tokenize_item_remove', [value]);
        self.updateSelect();
        self.setTextboxVisibility(!self.isFull());
        self.setInputFocus();
    };

    GUIControlTokenize.prototype.updateSelect = function(){
        let $previous, $option, values = [], self = this;

        if(self.settings.sortable){
            values = $('.token:not(.sortable-chosen)').map(function() {
                return $(this).attr("data-value");
            }).get();

            $.each(values, function(_, v){
                $option = $('option[value="' + self.escapeQuery(v) + '"]', self.$select);
                if($option.length){
                    if($previous === undefined){
                        $option.prependTo(self.$select);
                    } else {
                        $previous.after($option);
                    }
                    $previous = $option;
                }
            });

            self.$select.trigger('tokenize_item_reordered', [values]);
        }
    };

    GUIControlTokenize.prototype.onMouseDown = function(e){
        let self = this;

        if(e.button && e.button > 0){
            return;
        }

        if(self.settings.openOnMouseDown && !self.isOpen){
            self.refreshOptions(true);
        }
    };

    GUIControlTokenize.prototype.onFocus = function(){
        let self = this;

        if(self.settings.openOnFocus && !self.isOpen || self.$input.val() !== ''){
            self.refreshOptions(true);
        }
    };

    GUIControlTokenize.prototype.onBlur = function(){
        let self = this;

        if(self.isOpen){
            self.dropdownRemove();
        }
    };

    GUIControlTokenize.prototype.onKeyDown = function(e){
        let self = this;

        switch(e.keyCode){
            case KEY.BACKSPACE:
                if(self.$input.val() === ''){
                    let lastToken = $('.token:last', self.$container);
                    if(lastToken.length > 0){
                        if(lastToken.hasClass('await')){
                            self.removeItem(lastToken.attr('data-value'));
                        } else {
                            lastToken.addClass('await');
                        }
                    }
                }
                break;
            case KEY.TAB:
                if(!self.isShiftDown && self.settings.selectOnTab && self.$input.val() !== ''){
                    self.validateInput(e);
                }
                break;
            case KEY.UP:
                if(self.isOpen){
                    self.moveOptionsSelection(-1);
                }
                e.preventDefault();
                break;
            case KEY.DOWN:
                if(self.isOpen){
                    self.moveOptionsSelection(1);
                }
                e.preventDefault();
                break;
            case KEY.ENTER:
                self.validateInput(e);
                return;
            default:
                self.resetPending();
        }
    };

    GUIControlTokenize.prototype.onKeyPress = function(e){
        let self = this;
        let character = String.fromCharCode(e.keyCode || e.which);

        if(Array.isArray(self.settings.delimiter) && self.settings.delimiter.indexOf(character) >= 0){
            self.validateInput(e);
        } else if(character === self.settings.delimiter){
            self.validateInput(e);
        }
    };

    GUIControlTokenize.prototype.resetPending = function(){
        let self = this;
        let $token = $('.token.await', self.$container);

        if($token.length > 0){
            $token.removeClass('await');
        }
    };

    GUIControlTokenize.prototype.validateInput = function(e){
        let self = this;

        e.preventDefault();
        self.resetPending();

        if(self.isOpen && self.$activeOption){
            e.currentTarget = self.$activeOption;
            self.onOptionSelect(e);
        } else {
            self.setTextboxValue('');
        }
    };

    GUIControlTokenize.prototype.onInput = function(){
        let self = this;
        let value = self.$input.val() || '';
        if(self.lastValue !== value){
            self.lastValue = value;
            self.refreshOptions();
        }
    };

    GUIControlTokenize.prototype.setInputFocus = function(){
        let self = this;
        setTimeout(function(){ self.$input.trigger('focus') }, 20);
    };

    GUIControlTokenize.prototype.insertAtCaret = function(html){
        this.$input.before(html);
    };

    GUIControlTokenize.prototype.escapeHtml = function(str){
        return (str + '').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    };

    GUIControlTokenize.prototype.escapeQuery = function(str){
        return (str + '').replace(new RegExp(/\\/, 'g'), '\\\\');
    };

    GUIControlTokenize.prototype.autoGrow = function($input){
        let width, placeholderWidth, placeholder, diff;
        let self = this;

        $input.on('keydown keyup paste change blur', function(){
            diff = self.$input.outerWidth() - self.$input.width();
            placeholder = self.$input.attr('placeholder');

            if(placeholder && self.$input.val() === ''){
                placeholderWidth = self.measureString(placeholder, $input);
            } else {
                placeholderWidth = 0;
            }

            width = Math.max(self.measureString($input.val(), $input), placeholderWidth) + diff;

            if(width < self.$container.width()){
                self.$input.css('min-width', width);
            }

            self.updatePosition();
        });
    };

    GUIControlTokenize.prototype.measureString = function(str, $parent){
        let self = this;

        if(!str){
            return 0;
        }

        if(!self.$testInput){
            self.$testInput = $('<span />').css({
                position: 'absolute',
                width: 'auto',
                padding: 0,
                whiteSpace: 'pre'
            });

            $('<div />').css({
                position: 'absolute',
                width: 0,
                height: 0,
                overflow: 'hidden'
            }).attr({
                'aria-hidden': true
            }).append(self.$testInput).appendTo('body');
        }

        self.$testInput.text(str);

        self.transferStyles($parent, self.$testInput, [
            'letterSpacing',
            'fontSize',
            'fontFamily',
            'fontWeight',
            'textTransform'
        ]);

        return self.$testInput.width();
    };

    GUIControlTokenize.prototype.transferStyles = function($from, $to, properties){
        let i, n, styles = {};

        if(properties){
            for(i = 0, n = properties.length; i < n; i++){
                styles[properties[i]] = $from.css(properties[i]);
            }
        } else {
            styles = $from.css();
        }

        $to.css(styles);
    };

    GUIControlTokenize.DEFAULTS = {
        delimiter: ',',
        maxOptions: 10,
        maxItems: null,
        openOnFocus: false,
        openOnMouseDown: false,
        labelField: 'text',
        valueField: 'value',
        provider: 'select',
        providerQueryParameter: 'term',
        providerCache: false,
        sortable: true,
        setFirstOptionActive: true,
        selectOnTab: true,
        searchConjunction: 'and',
        searchRespectWordBoundaries: false,
        layoutDirection: 'row',
        render: {}
    };

    $.fn.GUIControlTokenize = function(option){
        $.each(this, function(){
            let $this = $(this);
            let data = $this.data('gui.tokenize');
            let options = typeof option == 'object' && option;

            if(!data) $this.data('gui.tokenize', new GUIControlTokenize(this, options));
        });
    };

}(jQuery);