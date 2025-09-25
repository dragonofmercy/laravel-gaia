import $ from 'jquery';
import { Collapse, Tooltip } from 'bootstrap';
import { Keycodes } from "./lib/keycodes.js";

export class GuiDatatable {

    constructor(element){
        this.$element = $(element).parent();

        this.$element.find('a[data-gui-url]').on('click', e => {
            e.preventDefault();
            GuiDatatable.browse($(e.currentTarget).blur().data('gui-url'), this.$element)
        });

        this._bindSearch();
        this._bindCheckboxSelectors();
    }

    _bindCheckboxSelectors(){
        this.$element.find('thead th:first-child input[type=checkbox]').on('change', e => {
            const status = $(e.currentTarget).prop('checked');
            this.$element.find('tbody tr td:first-child input[type=checkbox]').prop('checked', status).trigger('change.gui');
        })
    }

    _bindSearch(){
        const $searchContainer = this.$element.find('.datatable-search');

        gui.hideAttribute($searchContainer, 'data-search-url');

        if($searchContainer.length === 0){
            return;
        }

        $searchContainer.find('[data-trigger=search]').on('click', e => {
            e.preventDefault();
            this.search($searchContainer);
        });

        $searchContainer.find('[data-trigger=clear]').on('click', e => {
            e.preventDefault();
            this.search($searchContainer, true);
        });

        $searchContainer.find('[data-trigger=close]').on('click', e => {
            e.preventDefault();

            const $collapse = $searchContainer.parent();
            $collapse.on('hidden.bs.collapse.gui', () => {
                $collapse.off('hidden.bs.collapse.gui');
                GuiDatatable.browse($searchContainer.data('search-url'), this.$element, ['dt_c=1']);
            });

            Collapse.getOrCreateInstance($collapse[0], { toggle: false }).hide();
        });

        $searchContainer.find(':input[id^=dt_f]').on('keydown', e => {
            if(e.keyCode === Keycodes.ENTER){
                $searchContainer.find('[data-trigger=search]').click();
            }
        })
    }

    search($searchContainer, clear = false){
        const data = $searchContainer.find(':input[id^=dt_f]').serializeArray().map(v => v.name + '=' + (clear ? "" : v.value));
        GuiDatatable.browse($searchContainer.data('search-url'), this.$element, data);
    }

    static selector = function(target){
        const $element = $(target);

        gui.hideAttribute($element, 'data-target');
        gui.hideAttribute($element, 'data-url');
        gui.hideAttribute($element, 'data-query');
        gui.hideAttribute($element, 'data-remote');
        gui.hideAttribute($element, 'data-method');
        gui.hideAttribute($element, 'data-confirm');

        $element.on('click', e => {
            e.preventDefault();
            Tooltip.getInstance(target)?.hide();
            $element.blur();
            const $map = $('.gui-selector input[value]:checked', '#' + $element.data('target'));

            if($map.length == 0){
                return;
            }

            if($element.data('confirm')){
                if(!confirm($element.data('confirm'))){
                    return;
                }
            }

            const query = $map.map(function(){ return $(this).val(); }).toArray().join(',');

            if($element.data('remote')){
                GuiDatatable.browse($element.data('url'), "#" + $element.data('target'), [$element.data('query') + '=' + query], $element.data('method'));
            } else {
                const url = new URL($element.data('url'), window.location.origin);
                url.searchParams.set($element.data('query'), query);
                window.location.href = url.toString();
            }
        })
    }

    static reset = function(target){
        const $element = $(target);

        gui.hideAttribute($element, 'data-target');
        gui.hideAttribute($element, 'data-url');

        $element.on('click', e => {
            e.preventDefault();
            Tooltip.getInstance(target)?.hide();
            $element.blur();
            GuiDatatable.browse($element.data('url'), "#" + $element.data('target'), ['dt_c=1']);
        })
    }

    static browse = function(url, container, data = [], method = 'POST'){
        const $container = $(container);
        data.push('dt_u=' + $container.attr('id'));
        gui.remote(url, $container, data, method);
    }
}

$.fn.GuiDatatable = function(){
    return $.each(this, function(){
        const $this = $(this);
        const data = $this.data('gui.datatable');
        if(!data) $this.data('gui.datatable', new GuiDatatable(this));
    });
};

export default GuiDatatable;