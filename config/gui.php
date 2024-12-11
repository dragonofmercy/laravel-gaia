<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Translation loader configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the loading of the translations files like
    | countries list.
    |
    */

    "translations" => [
        'path' => [
            'countries' => '/vendor/petercoles/multilingual-country-list/data/{locale}.php'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Default view composer layout configuration
    |--------------------------------------------------------------------------
    |
    | This option is used for the default composer layout.
    |
    */

    'view_composer_layout' => 'gui::layout',

    /*
    |--------------------------------------------------------------------------
    | Forms field default size configuration
    |--------------------------------------------------------------------------
    |
    | This option is used to define the default form size used when gui-size is
    | not setted in form config.
    |
    | See sass source option $gui-form-field-sizes.
    |
    */

    'form_field_default_size' => 5,

    /*
    |--------------------------------------------------------------------------
    | Page title configuration
    |--------------------------------------------------------------------------
    |
    | This option is the default page title.
    |
    */

    'title' => "Laravel",

    /*
    |--------------------------------------------------------------------------
    | Metas tags configuration
    |--------------------------------------------------------------------------
    |
    | Use this array to define default meta tags.
    |
    */

    'metas' => [
        'charset' => "utf-8",
        'viewport' => "width=device-width, initial-scale=1, shrink-to-fit=no"
    ],

    /*
    |--------------------------------------------------------------------------
    | Metas properties configuration
    |--------------------------------------------------------------------------
    |
    | Use this array to define default meta properties.
    |
    */

    'metas_properties' => [
        'og:type' => "website",
    ],

    /*
    |--------------------------------------------------------------------------
    | Metas http configuration
    |--------------------------------------------------------------------------
    |
    | Use this array to define default meta http.
    |
    */

    'metas_http' => [
        'Content-Language' => 'en',
    ],

    /*
    |--------------------------------------------------------------------------
    | Links configuration
    |--------------------------------------------------------------------------
    |
    | Use this array to define default links (e.g. css include href).
    |
    */

    'links' => [
        '/assets/gui/gui.css'
    ],

    /*
    |--------------------------------------------------------------------------
    | Scripts include configuration
    |--------------------------------------------------------------------------
    |
    | Use this array to define default include script.
    |
    */

    'scripts' => [],

    /*
    |--------------------------------------------------------------------------
    | Deferred scripts include configuration
    |--------------------------------------------------------------------------
    |
    | Use this array to define default deferred include script, these includes
    | are placed at the end of the page.
    |
    */

    'deferred_script' => [
        '/assets/gui/gui.js'
    ],

    /*
    |--------------------------------------------------------------------------
    | Dark mode configuration
    |--------------------------------------------------------------------------
    |
    | Use theses options to configure dark mode such as replacements or force
    | color.
    |
    */

    'darkmode_force' => false,
    'darkmode_replacements' => [
        '/assets/gui/gui.css' => '/assets/gui/gui.dark.css'
    ],

];