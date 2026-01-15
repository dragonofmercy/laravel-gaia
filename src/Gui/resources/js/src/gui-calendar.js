import $ from 'jquery';
import IMask from 'imask';
import { DateTime, Info } from 'luxon';
import * as Popper from '@popperjs/core';

export class GuiCalendar {

    static DEFAULTS = {
        language: 'en',
        inline: false,
        min: null,
        max: null,
        initialDate: null,
        useMask: false,
        lazyMask: false,
        withTime: false,
        timeOnly: false,
        dateFormat: 'yyyy-MM-dd',
        timeFormat: 'HH:mm',
        trigger: 'self',
        minutesStep: 5,
        autoclose: true,
        displayToday: true,
        disabledDates: null,
        offset: 2,
        maskBlocks: {
            'dd': {mask: IMask.MaskedRange, from: 1, to: 31, maxLength: 2},
            'MM': {mask: IMask.MaskedRange, from: 1, to: 12, maxLength: 2},
            'yyyy': {mask: IMask.MaskedRange, from: 0, to: 9999, maxLength: 4},
            'HH': {mask: IMask.MaskedRange, from: 0, to: 23, maxLength: 2},
            'mm': {mask: IMask.MaskedRange, from: 0, to: 59, maxLength: 2}
        },
        strings: {today: "Today", now: "Now", clear: "Clear"},
        template: '<div class="gui-calendar">' +
            '<div class="dp-menu show">' +
            '<div class="dp-heading">' +
            '<a class="dp-prev"><svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24">' +
            '<path fill="currentColor" d="m19.496 4.136l-12 7a1 1 0 0 0 0 1.728l12 7A1 1 0 0 0 21 19V5a1 1 0 0 0-1.504-.864M4 4a1 1 0 0 1 .993.883L5 5v14a1 1 0 0 1-1.993.117L3 19V5a1 1 0 0 1 1-1" />' +
            '</svg></a>' +
            '<a class="dp-title"></a>' +
            '<a class="dp-next"><svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24">' +
            '<path fill="currentColor" d="M3 5v14a1 1 0 0 0 1.504.864l12-7a1 1 0 0 0 0-1.728l-12-7A1 1 0 0 0 3 5m17-1a1 1 0 0 1 .993.883L21 5v14a1 1 0 0 1-1.993.117L19 19V5a1 1 0 0 1 1-1" />' +
            '</svg></a>' +
            '</div>' +
            '<div class="dp-content"></div>' +
            '<div class="dp-footer">' +
            '<a class="dp-today"></a><a class="dp-clear"></a>' +
            '</div>' +
            '</div>' +
            '</div>'
    }

    constructor(element, options){
        this.options = $.extend({}, GuiCalendar.DEFAULTS, options);
        this.$element = $(element);

        try {
            Info.weekdays('short', {locale: this.options.language});
        } catch(e){
            console.error(e.message + ': ' + this.options.language);
        }

        this.$template = $(this.options.template);
        this.$prev = $('.dp-prev', this.$template);
        this.$next = $('.dp-next', this.$template);
        this.$title = $('.dp-title', this.$template);
        this.$btnToday = $('.dp-today', this.$template).html(this.options.strings.today);
        this.$btnClear = $('.dp-clear', this.$template).html(this.options.strings.clear);
        this.$content = $('.dp-content', this.$template);

        if(this.options.timeOnly){
            $('.dp-heading', this.$template).hide();
            this.$btnToday.html(this.options.strings.now);
        }

        this.options.min = this.options.min !== null ? DateTime.fromISO(this.options.min) : null;
        this.options.max = this.options.max !== null ? DateTime.fromISO(this.options.max) : null;

        if(this.options.inline){
            $('.dp-menu', this.$template).addClass('inline');
        } else {
            const trigger = this._resolveTrigger();

            if(trigger.is('input')){
                trigger.on('focus click', () => {
                    this.show();
                });
            } else {
                trigger.on('click', () => {
                    this.show();
                });
            }

            if(this._canUseMask()){
                this.mask = IMask(this.$element[0], {
                    mask: this._prepareFormat(),
                    overwrite: false,
                    lazy: this.options.lazyMask,
                    autofix: 'pad',
                    blocks: this.options.maskBlocks
                });

                if(this.options.lazyMask){
                    this.$element.on('blur.gui', () => {
                        this.mask.updateOptions({lazy: true});
                    }).on('focus.gui', () => {
                        this.mask.updateOptions({lazy: false});
                    })
                }
            }
        }

        if(this.options.inline){
            this.show();
        }
    }

    now(){
        return DateTime.now().setLocale(this.options.language);
    }

    show(){
        this._appendToContent();
        this._resetTimestamp();
        this._refreshDisplay();

        if(!this.options.inline){
            $(document).on('mousedown.gui.calendar touchend.gui.calendar', e => {
                if(!$(e.target).closest('.dp-menu').length && $('.dp-menu').is(":visible")) {
                    this.hide();
                }
            })
            this._popper = Popper.createPopper(this.$element.parent()[0], $('.dp-menu', this.$template)[0], this._getPopperConfig());
        }
    }

    hide(){
        if(!this.options.inline){
            $(document).off('mousedown.gui.calendar touchend.gui.calendar');
            this._popper.destroy();
            this.$template.remove();
        }
    }

    _resolveTrigger(){
        const trigger = this.options.trigger;

        if(trigger === 'self'){
            return this.$element;
        }

        if(typeof trigger === 'string'){
            return $(trigger);
        }

        if(typeof trigger === 'object' && trigger.selector){
            let $element = $(trigger.selector);

            if(trigger['chain'] && Array.isArray(trigger['chain'])){
                for(const step of trigger['chain']){
                    $element = $element[step['method']](...(step['args'] || []));
                }
            }

            return $element;
        }

        return trigger;
    }

    _canUseMask(){
        return this.options.useMask && this.$element.is('input');
    }

    _getPopperConfig(){
        return {
            placement: 'bottom-start',
            modifiers: [{
                name: 'offset',
                options: {
                    offset: [0, this.options.offset]
                }
            }]
        };
    }

    _resetTimestamp(){
        this.timestamp = this.now();
        this.selectedTimestamp = this.timestamp;

        if(null !== this.options.initialDate){
            this.timestamp = this._timestampFormat(this.options.initialDate) ?? this.now();
        }

        if(this.$element.is('input') && this.$element.val()){
            this.timestamp = this.selectedTimestamp = this._timestampFormat(this.$element.val()) ?? this.now();
        }
    }

    _clear(){
        this._setDate(null);
        if(this.options.autoclose){
            this.hide();
        }
    }

    _appendToContent(){
        if(!this.options.inline){
            $('body').append(this.$template);
        } else {
            if(this.$element.is('input')){
                this.$template.insertAfter(this.$element.parent());
            } else {
                this.$element.html(this.$template);
            }
        }

        this.$btnToday.on('click', () => {
            this._setDate(this.now());
        });

        this.$btnClear.on('click', () => {
            this._clear();
        });
    }

    _displayDays(){
        const $weekdays = $('<div class="weekdays" />');
        const $days = $('<div class="days" />');

        this._setTitle(this.timestamp.toFormat('MMMM yyyy'));
        this.$content.empty();
        this._bindNavigationEvents('days');

        $.each(Info.weekdays('short', {locale: this.options.language}), (_, day) => {
            $weekdays.append('<div class="weekday">' + this._ucFirst(day).substring(0, 2) + '</div>')
        });

        this.$content.append($weekdays);

        let renderDays = this.timestamp;
        const weekdayEndOfLastMonth = renderDays.minus({month: 1}).endOf('month').weekday;

        if(weekdayEndOfLastMonth < 7){
            renderDays = renderDays.minus({month: 1}).endOf('month').minus({days: weekdayEndOfLastMonth - 1});
        } else {
            renderDays = renderDays.minus({month: 1}).endOf('month').minus({days: 6});
        }

        for(let index = 0; index < 42; index++){
            const classes = ['day'];

            if(renderDays.month < this.timestamp.month){
                classes.push('prev');
            } else if(renderDays.month > this.timestamp.month){
                classes.push('next');
            }

            if(!this._inRange(renderDays, 'yyyy-LL-dd') || this._isDisabledDate(renderDays)){
                classes.push('disabled');
            } else {
                if(renderDays.toFormat('yyyy-LL-dd') === this.selectedTimestamp.toFormat('yyyy-LL-dd')){
                    classes.push('active');
                }
            }

            if(this.options.displayToday && renderDays.toFormat('yyyy-LL-dd') === this.now().toFormat('yyyy-LL-dd')){
                classes.push('today');
            }

            const $day = $('<a />')
                .addClass(classes)
                .data('gui.days.date', renderDays)
                .html(renderDays.toFormat('d'))
                .on('click', e => {
                    if(this.options.withTime){
                        this.timestamp = $(e.currentTarget).data('gui.days.date')
                        this._displayHours()
                    } else {
                        this._setDate($(e.currentTarget).data('gui.days.date'))
                    }
                });

            $days.append($day);

            renderDays = renderDays.plus({days: 1});
        }

        this.$content.append($days);
    }

    _displayMonths(){
        const $months = $('<div class="months" />');
        const months = Info.months('short', {locale: this.options.language});

        this._setTitle(this.timestamp.toFormat('yyyy'));
        this.$content.empty();
        this._bindNavigationEvents('months');

        months.forEach((month, index) => {
            const monthNumber = index + 1;
            const classes = ['month'];

            if(!this._inRange(this.timestamp.set({ month: (index + 1)}), 'yyyy-LL')){
                classes.push('disabled');
            } else {
                if((index + 1) === this.selectedTimestamp.month){
                    classes.push('active');
                }
            }

            const $month = $('<a />')
                .addClass(classes.join(' '))
                .html(this._ucFirst(month))
                .on('click', () => {
                    this.timestamp = this.timestamp.set({month: monthNumber})
                    this._displayDays()
                });

            $months.append($month);
        })

        this.$content.append($months);
    }

    _displayYears(){
        const startingYear = parseInt(this.timestamp.year / 10, 10) * 10;
        const paneTs = DateTime.fromFormat(startingYear + '-01-01', 'yyyy-LL-dd');
        const $years = $('<div class="years" />');

        this._setTitle(paneTs.toFormat('yyyy') + "-" + paneTs.plus({year: 9}).toFormat('yyyy'), true);
        this.$content.empty();
        this._bindNavigationEvents('years');

        for(let year = startingYear; year < (startingYear + 10); year++){
            const classes = ['year'];

            if(!this._inRange(this.timestamp.set({ year: year}), 'yyyy')){
                classes.push('disabled');
            } else {
                if(year === this.selectedTimestamp.year){
                    classes.push('active');
                }
            }

            const $year = $('<a />')
                .addClass(classes)
                .html(year)
                .on('click', () => {
                    this.timestamp = this.timestamp.set({year: year})
                    this._displayMonths()
                });

            $years.append($year);
        }

        this.$content.append($years);
    }

    _displayHours(){
        const $hours = $('<div class="hours" />');

        this._setTitle(this.timestamp.toFormat('d LLLL yyyy'), this.options.timeOnly);
        this.$content.empty();

        if(!this.options.timeOnly){
            this._bindNavigationEvents('hours');
        }

        for(let hour = 0; hour < 24; hour++){
            const classes = ['hour'];

            if(!this._inRange(this.timestamp.set({hour: hour}), "yyyy-LL-dd'T'HH:00")){
                classes.push('disabled');
            }

            const $hour = $('<a />')
                .addClass(classes)
                .html((hour < 10 ? "0" + hour : hour) + ":00")
                .on('click', () => {
                    this.timestamp = this.timestamp.set({hour: hour})
                    this._displayMinutes()
                });

            $hours.append($hour);
        }

        this.$content.append($hours);
    }

    _displayMinutes(){
        const $minutes = $('<div class="minutes" />');

        this._setTitle(this.timestamp.toFormat('d LLLL yyyy'));
        this.$content.empty();
        this._bindNavigationEvents('minutes');

        for(let minute = 0; minute < 60; minute += this.options.minutesStep){
            const classes = ['minute'];

            if(!this._inRange(this.timestamp.set({minute: minute}), "yyyy-LL-dd'T'HH:mm")){
                classes.push('disabled');
            }

            const $minute = $('<a />')
                .addClass(classes)
                .html(this.timestamp.toFormat('HH') + ":" + (minute < 10 ? "0" + minute : minute))
                .on('click', () => {
                    this.timestamp = this.timestamp.set({minute: minute})
                    this._setDate(this.timestamp)
                });

            $minutes.append($minute);
        }

        this.$content.append($minutes);
    }

    _inRange(timestamp, format){
        if(this.options.min == null && this.options.max === null){
            return true;
        }

        format = format || 'yyyy-LL-dd';

        const source = DateTime.fromISO(timestamp.toFormat(format));

        if(this.options.min !== null){
            if(source < DateTime.fromISO(this.options.min.toFormat(format))){
                return false;
            }
        }

        if(this.options.max !== null){
            if(source > DateTime.fromISO(this.options.max.toFormat(format))){
                return false;
            }
        }

        return true;
    }

    _isDisabledDate(timestamp){
        if(this.options.disabledDates !== null){
            for(let i = 0; i < this.options.disabledDates.length; i++){
                if(this.options.disabledDates[i] === timestamp.toFormat('yyyy-LL-dd')){
                    return true;
                }
            }
        }

        return false;
    }

    _bindNavigationEvents(view) {
        const navigationConfig = {
            days: {
                prev: () => this.timestamp.startOf('month').minus({month: 1}),
                next: () => this.timestamp.startOf('month').plus({month: 1}),
                title: () => this._displayMonths(),
                refresh: () => this._displayDays()
            },
            months: {
                prev: () => this.timestamp.minus({year: 1}),
                next: () => this.timestamp.plus({year: 1}),
                title: () => this._displayYears(),
                refresh: () => this._displayMonths()
            },
            years: {
                prev: () => this.timestamp.minus({year: 10}),
                next: () => this.timestamp.plus({year: 10}),
                title: null,
                refresh: () => this._displayYears()
            },
            hours: {
                prev: () => this.timestamp.minus({day: 1}),
                next: () => this.timestamp.plus({day: 1}),
                title: () => this._displayDays(),
                refresh: () => this._displayHours()
            }
        };

        const config = navigationConfig[view];
        if (!config) return;

        this.$prev.off('click').on('click', () => {
            this.timestamp = config.prev();
            config.refresh();
        });

        this.$next.off('click').on('click', () => {
            this.timestamp = config.next();
            config.refresh();
        });

        if(config.title && !this.options.timeOnly){
            this.$title.off('click').on('click', config.title);
        } else {
            this.$title.off('click');
        }
    }

    _setDate(timestamp){
        let output = null;

        if(timestamp !== null){
            this.timestamp = timestamp;

            if(this.options.min !== null && this.timestamp < DateTime.fromISO(this.options.min)){
                this.timestamp = DateTime.fromISO(this.options.min);
            }

            if(this.options.max !== null && this.timestamp > DateTime.fromISO(this.options.max)){
                this.timestamp = DateTime.fromISO(this.options.max);
            }

            this.selectedTimestamp = this.timestamp;

            output = this.timestamp.toFormat(this._prepareFormat());
        } else {
            this._resetTimestamp();
        }

        this._refreshDisplay();

        if(this.$element.is('input')){
            this.$element.val(output);
            if(this._canUseMask()){
                this.mask.updateValue();
            }
        }

        this.$element.trigger({
            type: 'gui.calendar.change',
            date: timestamp !== null ? this.timestamp.toJSDate() : null,
            iso: timestamp !== null ? this.timestamp.toISODate() : null,
            formated: output
        }).trigger('change');

        if(this.options.autoclose){
            this.hide();
        }
    }

    _refreshDisplay() {
        if(this.options.timeOnly){
            this._displayHours();
        } else {
            this._displayDays();
        }
    }

    _setTitle(title, disable){
        this.$title.css('pointer-events', disable ? 'none' : '').html(this._ucFirst(title));
    }

    _ucFirst(string){
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    _timestampFormat(value){
        if(null === value){
            return null;
        }
        const tmp = DateTime.fromFormat(value, this._prepareFormat(), { locale: this.options.language });
        return tmp.isValid ? tmp : null;
    }

    _prepareFormat(){
        if(this.options.timeOnly){
            return this.options.timeFormat;
        } else {
            if(this.options.withTime){
                return this.options.dateFormat + ' ' + this.options.timeFormat;
            } else {
                return this.options.dateFormat;
            }
        }
    }
}

$.fn.GuiCalendar = function(option){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.calendar');
        const options = typeof option == 'object' && option;
        if(!data) $this.data('gui.calendar', new GuiCalendar(this, options));
    });
};

export default GuiCalendar;