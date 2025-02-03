<?php
return [

    'required' => "Required field.",
    'invalid' => "Incorrect value.",

    'string' => [
        'min_length' => "The value is too short (:min_length characters min).",
        'max_length' => "The value is too long (:max_length characters max).",
    ],

    'number' => [
        'min' => "The value is too small (min :min).",
        'max' => "The value is too big (max :max).",
        'invalid' => ":value is not a number.",
    ],

    'choice' => [
        'required' => "Please select a value.",
        'min' => "Please select at least :min values.",
        'max' => "Please select at most :max values.",
        'unique' => "An object with the same value already exist.",
    ],

    'email' => [
        'invalid' => "The email format is incorrect.",
    ],

    'url' => [
        'invalid' => "The URL format is incorrect.",
    ],

    'date' => [
        'min' => "The date must be greather than \":min\".",
        'max' => "The date must be earlier than \":max\".",
    ],

    'line' => [
        'min' => "Please enter at least :min lines.",
        'max' => "Please enter at most :max lines.",
    ],

    'file' => [
        'min' => "Please choose at least :min files.",
        'max' => "Please choose at most :max files.",
        'max_size' => "\":name\" is too large (max :size).",
        'min_size' => "\":name\" is too small (min :size).",
        'mime_types' => "Invalid mime type \":mime\" for \":name\".",
        'max_width' => "\":name\" is too wide (max :width).",
        'min_width' => "\":name\" is not enought wide (min :width).",
        'max_height' => "\":name\" is too tall (max :height).",
        'min_height' => "\":name\" is not enought tall (min :height).",
    ],

    'captcha' => [
        'invalid' => "The captcha is invalid",
    ],

];