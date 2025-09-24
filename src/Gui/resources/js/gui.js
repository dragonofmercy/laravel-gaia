import $ from 'jquery';
import { Dropdown, Tooltip, Popover, Toast } from 'bootstrap';

import './src/gui-calendar.js';
import './src/gui-control-autocomplete.js';
import './src/gui-control-checklist.js';
import './src/gui-control-code.js';
import './src/gui-control-file.js';
import './src/gui-control-image.js';
import './src/gui-control-ipv4.js';
import './src/gui-control-lines.js';
import './src/gui-control-multi-fields.js';
import './src/gui-control-number.js';
import './src/gui-control-password.js';
import './src/gui-control-token.js';
import './src/gui-copy.js';
import './src/gui-loading-button.js';
import './src/gui-modal.js';
import './src/gui-tabs.js';
import './src/gui-theme-toggle.js';

import GuiDatatable from "./src/gui-datatable.js";

$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' }
});

window.jQuery = $;
window.$ = $;

window.GuiDatatable = GuiDatatable;
window.bootstrap = { Dropdown, Tooltip, Popover, Toast };

export class Gui {

    /**
     * Initializes various UI components and features by invoking specific setup methods for bootstrap elements, range controls, tabs, button loading states, modals, and theme toggling within the specified context.
     *
     * @param {Document|HTMLElement} context - The DOM context within which the components will be initialized. Defaults to the entire document if not specified.
     * @return {void} This method does not return any value.
     */
    init(context = document){
        this._initBootstrap(context);
        this._initDatatables(context);
        this._initRange(context);
        this._initTabs(context);
        this._initButtonLoading(context);
        this._initModal(context);
        this._initCopy(context);
        this._initThemeToggle(context);
    }

    /**
     * Displays an error message or an iframe containing error details in the specified container.
     *
     * @param {Object} e - The error object containing the status and responseText properties.
     * @param {string|HTMLElement|jQuery} container - The container where the error message or iframe will be rendered.
     * @return {void}
     */
    printError(e, container){
        const $container = $(container);

        if(!e.responseText){
            return;
        }

        if(e.responseText.indexOf('<!DOCTYPE html') !== -1){
            const iframe = document.createElement('iframe');
            iframe.srcdoc = e.responseText;
            $container.html($(iframe).css({width: '100%', height: '70vh'}));
        } else {
            $container.html(e.responseText);
        }
    }

    /**
     * Hides the specified attribute of the given jQuery element by storing its value in the element's data storage
     * and then removing the attribute from the element.
     *
     * @param {Object} $element - The jQuery element whose attribute is to be hidden.
     * @param {string} attribute - The name of the attribute to hide. If it starts with 'data-', only the key portion is stored.
     * @return {void} This method does not return a value.
     */
    hideAttribute($element, attribute){
        const attributeKey = attribute.startsWith('data-') ? attribute.substring(5) : attribute;
        $element.data(attributeKey, $element.attr(attribute));
        $element.removeAttr(attribute);
    }

    /**
     * Initializes datatable components within a given context.
     *
     * @param {HTMLElement|jQuery} context The DOM element or jQuery object that contains the elements to initialize as datatables.
     * @return {void}
     */
    _initDatatables(context){
        $('[data-gui-behavior="datatable"]', context).GuiDatatable();
        $('[data-gui-behavior="datatable-selector"]', context).each((_, e) => {
            GuiDatatable.selector(e)
        })
        $('[data-gui-behavior="datatable-reset"]', context).each((_, e) => {
            GuiDatatable.reset(e)
        })
    }

    /**
     * Initializes the tab components within the given context.
     *
     * @param {HTMLElement|jQuery} context - The context in which to search for tab elements to initialize.
     * @return {void} This method does not return a value.
     */
    _initTabs(context){
        $('[data-gui-behavior="tabs"]', context).GuiTabs();
    }

    /**
     * Initializes the theme toggle functionality for elements matching the specified selector within the given context.
     *
     * @param {jQuery|HTMLElement} context - The context in which to find and initialize theme toggle elements.
     * @return {void} This method does not return a value.
     */
    _initThemeToggle(context){
        $('[data-gui-behavior="theme-toggle"]:not(.disabled):not(:disabled)', context).GuiThemeToggle();
    }

    /**
     * Initializes the copy functionality within the provided context by enabling elements
     * that have the data-gui-behavior attribute set to "copy".
     *
     * @param {Object} context - The DOM context in which the copy functionality should be initialized.
     * @return {void} This method does not return any value.
     */
    _initCopy(context){
        $('[data-gui-behavior="copy"]', context).GuiCopy();
    }

    /**
     * Initializes button loading functionality within the given context.
     * Binds a click event to elements matching the selector `[data-loading-text]:not(.disabled):not(:disabled)`
     * inside the provided context, enabling the loading state for the clicked button.
     *
     * @param {HTMLElement|jQuery} context - The DOM element or jQuery object that serves as the context within which buttons are initialized.
     * @return {void} This method does not return a value.
     */
    _initButtonLoading(context){
        $('[data-loading-text]:not(.disabled):not(:disabled)', context).on('click.gui.button', function(){
            $(this).GuiLoadingButton('loading');
        });
    }

    /**
     * Initializes modal functionality within the given context by attaching a click event listener
     * to elements marked with a specific data attribute. The event triggers the modal display logic.
     *
     * @param {Object} context - The DOM context in which the modal initialization should occur.
     * @return {void}
     */
    _initModal(context){
        $('[data-gui-behavior="modal"]:not(.disabled):not(:disabled)', context).on('click.gui.modal', function(e){
            e.preventDefault();
            $(this).GuiModal().data('gui.modal').show();
        });
    }

    /**
     * Initializes and applies custom styles and event listeners for input elements with the class 'form-range with-progress'
     * within the specified context to handle range values dynamically.
     *
     * @param {Object} context - The DOM context in which to find and initialize range elements.
     * @return {void}
     */
    _initRange(context){
        $('input[type="range"].with-progress', context).each(function(){
            $(this).css({
                '--value': $(this).val(),
                '--min': $(this).attr('min') ?? 0,
                '--max': $(this).attr('max') ?? 100
            });
            $(this).on('input', function(){
                $(this).css('--value', $(this).val());
            });
        });
    }

    /**
     * Initializes Bootstrap components such as dropdowns within the provided context.
     *
     * @param {HTMLElement|jQuery} context - The DOM element or jQuery object containing the elements to initialize.
     * @return {void} No return value.
     */
    _initBootstrap(context){
        $('[data-bs-toggle="dropdown"], [data-gui-toggle="dropdown"]', context).each((_, e) => {
            const element = $(e);
            const options = {
                boundary: element.attr('data-bs-boundary') === 'viewport' ? document.querySelector('.btn') : 'clippingParents',
            };
            element.data('bs-dropdown', new Dropdown(element[0], options));
        });

        $('[data-bs-toggle="tooltip"], [data-gui-toggle="tooltip"]', context).each((_, e) => {
            const element = $(e);
            const options = {
                animation: false,
                delay: {show: 50, hide: 50},
                html: element.attr('data-bs-html') === "true" ?? false,
                placement: element.attr('data-bs-placement') ?? 'top'
            };

            element.data('bs-tooltip', new Tooltip(element[0], options));
        });

        $('[data-bs-toggle="popover"], [data-gui-toggle="popover"]', context).each((_, e) => {
            const element = $(e);
            const options = {
                delay: {show: 50, hide: 50},
                html: element.attr('data-bs-html') === "true" ?? false,
                placement: element.attr('data-bs-placement') ?? 'auto'
            };
            element.data('bs-popover', new Popover(element[0], options));
        });
    }
}

window.gui = new Gui();
window.gui.init();