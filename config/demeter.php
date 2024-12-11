<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Blade minifier
    |--------------------------------------------------------------------------
    |
    | Use minify options to activate the blade minifer.
    | minify_ignore option can be used to set ignored paths.
    |
    */

    'minify' => false,
    'minify_ignore' => ['Illuminate/Mail/resources/views', 'Illuminate/Notifications/resources/views'],

    /*
    |--------------------------------------------------------------------------
    | Hashing modules
    |--------------------------------------------------------------------------
    |
    | Use hasing options to configure additional demeter hashing options
    |
    */

    'openssl_passphrase' => env('APP_KEY')

];