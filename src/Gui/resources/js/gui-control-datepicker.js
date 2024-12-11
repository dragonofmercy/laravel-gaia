!function($){
    'use strict';

    let GUIControlDatePicker = function(element, options){
        let $this = this;

        this.$element  = $(element);
        this.options   = $.extend({}, GUIControlDatePicker.DEFAULTS, options);

        // Define Luxon DateTime
        this.DateTime = luxon.DateTime;

        try{
            luxon.Info.weekdays('short', {locale: this.options.language});
        } catch(e) {
            console.error(e.message + ': ' + this.options.language);
            return;
        }

        this.now = this.DateTime.now().setLocale(this.options.language)
        this.timestamp = this.DateTime.now().setLocale(this.options.language);
        this.selectedTimestamp = this.timestamp;

        // Define globals
        this.$template = $(this.options.template);
        this.$prev = $('.dp-prev', this.$template);
        this.$next = $('.dp-next', this.$template);
        this.$title = $('.dp-title', this.$template);
        this.$btnToday = $('.dp-today', this.$template).html(this.options.strings.today);
        this.$btnClear = $('.dp-clear', this.$template).html(this.options.strings.clear);
        this.$content = $('.dp-content', this.$template);

        if(this.options.timeOnly){
            $('.dp-heading', this.$template).hide();
            this.$element.addClass('text-center');
            this.$btnToday.html(this.options.strings.now);
        }

        this.options.trigger = this.options.trigger === 'self' ? this.$element : this.options.trigger;
        this.options.min = this.options.min !== null ? this.DateTime.fromISO(this.options.min) : null;
        this.options.max = this.options.max !== null ? this.DateTime.fromISO(this.options.max) : null;

        if(this.options.inline){
            $('.dp-menu', this.$template).addClass('inline');
        } else {
            if(this.options.trigger instanceof jQuery){
                if(this.options.trigger.is('input')){
                    this.options.trigger.on('focus click', function(){
                        $this.show();
                    });
                } else {
                    this.options.trigger.on('click', function(){
                        $this.show();
                    });
                }
            }
        }

        this.$btnToday.on('click', function(){
            $this.setDate($this.now);
        });

        this.$btnClear.on('click', function(){
            $this.clear();
        });

        this.appendToContent();

        if(this.options.inline){
            this.show();
        }
    };

    GUIControlDatePicker.prototype.show = function(){
        let $this = this;

        if(this.$element.is('input') && this.$element.val()){
            let tmp = this.DateTime.fromFormat(this.$element.val(), this.parsingFormat(), { locale: this.options.language });
            if(tmp.isValid){
                this.timestamp = this.selectedTimestamp = tmp;
            }
        }

        if(this.options.timeOnly){
            this.displayHours();
        } else {
            this.displayDays();
        }

        if(!this.options.inline){
            this.updatePosition();

            $(window).on('resize.gui.datepicker scroll.gui.datepicker', function(){
                $this.updatePosition();
            });

            $(document).on('mousedown.gui.datepicker touchend.gui.datepicker', function(e){
                if(!$(e.target).closest('.dp-menu').length && $('.dp-menu').is(":visible")) {
                    $this.hide();
                }
            });
        }

        $('.dp-menu', this.$template).css('display', 'inline-flex');
    };

    GUIControlDatePicker.prototype.hide = function(){
        if(!this.options.inline){
            $('.dp-menu', this.$template).hide();
            $(document).off('mousedown.gui.datepicker touchend.gui.datepicker');
            $(window).off('resize.gui.datepicker scroll.gui.datepicker');
        }
    };

    GUIControlDatePicker.prototype.displayDays = function(){
        let $this = this;
        let $weekdays = $('<div class="weekdays" />');
        let $days = $('<div class="days" />');

        this.setTitle(this.timestamp.toFormat('MMMM yyyy'));
        this.$content.empty();

        this.$prev.off('click').on('click', function(){
            $this.timestamp = $this.timestamp.startOf('month').minus({month: 1});
            $this.displayDays();
        });

        this.$next.off('click').on('click', function(){
            $this.timestamp = $this.timestamp.startOf('month').plus({month: 1});
            $this.displayDays();
        });

        this.$title.off('click').on('click', function(){
            $this.displayMonths();
        });

        $.each(luxon.Info.weekdays('short', {locale: this.options.language}), function(_, day){
            $weekdays.append('<div class="weekday">' + $this.ucFirst(day).substring(0, 2) + '</div>');
        });

        this.$content.append($weekdays);

        let renderDays = this.timestamp;
        let weekdayEndOfLastMonth = renderDays.minus({month: 1}).endOf('month').weekday;

        if(weekdayEndOfLastMonth < 7){
            renderDays = renderDays.minus({month: 1}).endOf('month').minus({days: weekdayEndOfLastMonth - 1});
        } else {
            renderDays = renderDays.minus({month: 1}).endOf('month').minus({days: 6});
        }

        for(let index = 0; index < 42; index++){
            let classes = new Array;
            classes.push('day');

            if(renderDays.month < this.timestamp.month){
                classes.push('prev');
            } else if(renderDays.month > this.timestamp.month){
                classes.push('next');
            }

            if(!this.inRange(renderDays, 'yyyy-LL-dd') || this.isDisabledDate(renderDays)){
                classes.push('disabled');
            } else {
                if(renderDays.toFormat('yyyy-LL-dd') === this.selectedTimestamp.toFormat('yyyy-LL-dd')){
                    classes.push('active');
                }
            }

            if(this.options.displayToday && renderDays.toFormat('yyyy-LL-dd') === this.now.toFormat('yyyy-LL-dd')){
                classes.push('today');
            }

            let $day = $('<a />')
                .addClass(classes)
                .data('gui.days.date', renderDays)
                .html(renderDays.toFormat('d'))
                .on('click', function(){
                    if($this.options.withTime){
                        $this.timestamp = $(this).data('gui.days.date');
                        $this.displayHours();
                    } else {
                        $this.setDate($(this).data('gui.days.date'));
                    }
                });

            $days.append($day);
            renderDays = renderDays.plus({days: 1});
        }

        this.$content.append($days);
    };

    GUIControlDatePicker.prototype.displayMonths = function(){
        let $this = this;
        let $months = $('<div class="months" />');
        let months = luxon.Info.months('short', {locale: this.options.language});

        this.setTitle(this.timestamp.toFormat('yyyy'));
        this.$content.empty();

        this.$prev.off('click').on('click', function(){
            $this.timestamp = $this.timestamp.minus({year: 1});
            $this.displayMonths();
        });

        this.$next.off('click').on('click', function(){
            $this.timestamp = $this.timestamp.plus({year: 1});
            $this.displayMonths();
        });

        this.$title.off('click').on('click', function(){
            $this.displayYears();
        });

        for(let index = 0; index < months.length; index++){
            let classes = new Array;
            classes.push('month');

            if(!this.inRange(this.timestamp.set({ month: (index + 1)}), 'yyyy-LL')){
                classes.push('disabled');
            } else {
                if((index + 1) === this.selectedTimestamp.month){
                    classes.push('active');
                }
            }

            let $month = $('<a />')
                .addClass(classes)
                .html($this.ucFirst(months[index]))
                .on('click', function(){
                    $this.timestamp = $this.timestamp.set({ month: (index + 1)});
                    $this.displayDays();
                });

            $months.append($month);
        }

        this.$content.append($months);
    };

    GUIControlDatePicker.prototype.displayYears = function(){
        let $this = this;
        let startingYear = parseInt(this.timestamp.year / 10, 10) * 10;
        let paneTs = this.DateTime.fromFormat(startingYear + '-01-01', 'yyyy-LL-dd');
        let $years = $('<div class="years" />');

        this.setTitle(paneTs.toFormat('yyyy') + "-" + paneTs.plus({year: 9}).toFormat('yyyy'), true);
        this.$content.empty();

        this.$prev.off('click').on('click', function(){
            $this.timestamp = paneTs.minus({year: 10});
            $this.displayYears();
        });

        this.$next.off('click').on('click', function(){
            $this.timestamp = paneTs.plus({year: 10});
            $this.displayYears();
        });

        for(let year = startingYear; year < (startingYear + 10); year++){
            let classes = new Array;
            classes.push('year');

            if(!this.inRange(this.timestamp.set({ year: year}), 'yyyy')){
                classes.push('disabled');
            } else {
                if(year === this.selectedTimestamp.year){
                    classes.push('active');
                }
            }

            let $year = $('<a />')
                .addClass(classes)
                .html(year)
                .on('click', function(){
                    $this.timestamp = $this.timestamp.set({year: year});
                    $this.displayMonths();
                });

            $years.append($year);
        }

        this.$content.append($years);
    };

    GUIControlDatePicker.prototype.displayHours = function(){
        let $this = this;
        let $hours = $('<div class="hours" />');

        this.setTitle(this.timestamp.toFormat('d LLLL yyyy'), this.options.timeOnly);
        this.$content.empty();

        if(!this.options.timeOnly){
            this.$prev.off('click').on('click', function(){
                $this.timestamp = $this.timestamp.minus({day: 1});
                $this.displayHours();
            });

            this.$next.off('click').on('click', function(){
                $this.timestamp = $this.timestamp.plus({day: 1});
                $this.displayHours();
            });

            this.$title.off('click').on('click', function(){
                $this.displayDays();
            });
        }

        for(let hour = 0; hour < 24; hour++){
            let classes = new Array;
            classes.push('hour');

            if(!this.inRange(this.timestamp.set({hour: hour}), "yyyy-LL-dd'T'HH:00")){
                classes.push('disabled');
            }

            let $hour = $('<a />')
                .addClass(classes)
                .html((hour < 10 ? "0" + hour : hour) + ":00")
                .on('click', function(){
                    $this.timestamp = $this.timestamp.set({hour: hour});
                    $this.displayMinutes();
                });

            $hours.append($hour);
        }

        this.$content.append($hours);
    };

    GUIControlDatePicker.prototype.displayMinutes = function(){
        let $this = this;
        let $minutes = $('<div class="minutes" />');

        this.setTitle(this.timestamp.toFormat('d LLLL yyyy'));
        this.$content.empty();

        this.$prev.off('click').on('click', function(){
            $this.timestamp = $this.timestamp.minus({day: 1});
            $this.displayHours();
        });

        this.$next.off('click').on('click', function(){
            $this.timestamp = $this.timestamp.plus({day: 1});
            $this.displayHours();
        });

        this.$title.off('click').on('click', function(){
            $this.displayHours();
        });

        for(let minute = 0; minute < 60; minute += this.options.minutesStep){
            let classes = new Array;
            classes.push('minute');

            if(!this.inRange(this.timestamp.set({minute: minute}), "yyyy-LL-dd'T'HH:mm")){
                classes.push('disabled');
            }

            let $minute = $('<a />')
                .addClass(classes)
                .html(this.timestamp.toFormat('HH') + ":" + (minute < 10 ? "0" + minute : minute))
                .on('click', function(){
                    $this.timestamp = $this.timestamp.set({minute: minute});
                    $this.setDate($this.timestamp);
                });

            $minutes.append($minute);
        }

        this.$content.append($minutes);
    };

    GUIControlDatePicker.prototype.setTitle = function(title, disable){
        this.$title.css('pointer-events', disable ? 'none' : '').html(this.ucFirst(title));
    };

    GUIControlDatePicker.prototype.appendToContent = function(){
        if(!this.options.inline){
            $('body').append(this.$template);
        } else {
            if(this.$element.is('input')){
                this.$template.insertAfter(this.$element.parent());
            } else {
                this.$element.html(this.$template);
            }
        }
    };

    GUIControlDatePicker.prototype.setDate = function(timestamp){
        let output;

        if(timestamp !== null){
            this.timestamp = timestamp

            if(this.options.min !== null && this.timestamp < this.DateTime.fromISO(this.options.min)){
                this.timestamp = this.DateTime.fromISO(this.options.min);
            }

            if(this.options.max !== null && this.timestamp > this.DateTime.fromISO(this.options.max)){
                this.timestamp = this.DateTime.fromISO(this.options.max);
            }

            output = this.timestamp.toFormat(this.parsingFormat());
        } else {
            // Reset calendar to default
            this.timestamp = this.now;
        }

        this.selectedTimestamp = this.timestamp;

        if(this.options.timeOnly){
            this.displayHours();
        } else {
            this.displayDays();
        }

        if(this.$element.is('input')){
            this.$element.val(output);
        }

        this.$element.trigger({
            type: 'gui.datepicker.change',
            date: timestamp !== null ? this.timestamp.toJSDate() : null,
            iso: timestamp !== null ? this.timestamp.toISODate() : null,
            formated: output
        }).trigger('change');

        if(this.options.autoclose){
            this.hide();
        }
    };

    GUIControlDatePicker.prototype.clear = function(){
        this.setDate(null);
        if(this.options.autoclose){
            this.hide();
        }
    };

    GUIControlDatePicker.prototype.updatePosition = function(){
        let position = this.$element.offset();
        position.top += this.$element.outerHeight();
        $('.dp-menu', this.$template).css({top: position.top, left: position.left});
    };

    GUIControlDatePicker.prototype.inRange = function(timestamp, format){
        if(this.options.min == null && this.options.max === null){
            return true;
        }

        format = format || 'yyyy-LL-dd';

        let source = this.DateTime.fromISO(timestamp.toFormat(format));

        if(this.options.min !== null){
            if(source < this.DateTime.fromISO(this.options.min.toFormat(format))){
                return false;
            }
        }

        if(this.options.max !== null){
            if(source > this.DateTime.fromISO(this.options.max.toFormat(format))){
                return false;
            }
        }

        return true;
    };

    GUIControlDatePicker.prototype.isDisabledDate = function(timestamp){
        if(this.options.disabledDates !== null){
            for(let i = 0; i < this.options.disabledDates.length; i++){
                if(this.options.disabledDates[i] === timestamp.toFormat('yyyy-LL-dd')){
                    return true;
                }
            }
        }

        return false;
    };

    GUIControlDatePicker.prototype.ucFirst = function(string){
        return string.charAt(0).toUpperCase() + string.slice(1);
    };

    GUIControlDatePicker.prototype.parsingFormat = function(){
        if(this.options.timeOnly){
            return this.options.timeFormat;
        } else {
            if(this.options.withTime){
                return this.options.dateFormat + ' ' + this.options.timeFormat;
            } else {
                return this.options.dateFormat;
            }
        }
    };

    GUIControlDatePicker.prototype.setOption = function(name, value){
        if(value !== null && (name == 'min' || name == 'max')){
            value = this.DateTime.fromISO(value);
        }

        this.options[name] = value;
    };

    GUIControlDatePicker.DEFAULTS = {
        language: 'en',
        inline: false,
        min: null,
        max: null,
        withTime: false,
        timeOnly: false,
        dateFormat: 'yyyy-LL-dd',
        timeFormat: 'HH:mm',
        trigger: 'self',
        minutesStep: 5,
        autoclose: true,
        displayToday: true,
        disabledDates: null,
        strings: {today: "Today", now: "Now", clear: "Clear"},
        template: '<div class="gui-control-datepicker">' +
            '<div class="dp-menu">' +
            '<div class="dp-heading">' +
            '<a class="dp-prev"><i class="fa-solid fa-backward-step"></i></a>' +
            '<a class="dp-title"></a>' +
            '<a class="dp-next"><i class="fa-solid fa-forward-step"></i></a>' +
            '</div>' +
            '<div class="dp-content"></div>' +
            '<div class="dp-footer">' +
            '<a class="dp-today"></a><a class="dp-clear"></a>' +
            '</div>' +
            '</div>' +
            '</div>'
    };

    $.fn.GUIControlDatePicker = function(option){
        $.each(this, function(){
            let $this = $(this);
            let data = $this.data('gui.datepicker');
            let options = typeof option == 'object' && option;

            if(!data) $this.data('gui.datepicker', new GUIControlDatePicker(this, options));
        });
    };

}(jQuery);