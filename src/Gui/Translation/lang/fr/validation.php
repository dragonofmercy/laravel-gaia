<?php
return [

    'required' => "Ce champ doit être rempli.",
    'invalid' => "Valeur incorrecte.",

    'string' => [
        'min_length' => "La valeur est trop petite (:min_length caractère(s) min).",
        'max_length' => "La valeur est trop grande (:max_length caractères max).",
    ],

    'number' => [
        'min' => "La valeur est trop petite (min :min).",
        'max' => "La valeur est trop grande (max :max).",
        'invalid' => ":value is not a number.",
    ],

    'choice' => [
        'required' => "Veuillez choisir une valeur.",
        'min' => "Veuillez choisir au moins :min valeurs.",
        'max' => "Veuillez choisir au maximum :max valeurs.",
        'unique' => "Un enregistrement avec cette valeur existe déjà.",
    ],

    'email' => [
        'invalid' => "Le format de l'adresse e-mail est incorrect.",
    ],

    'url' => [
        'invalid' => "Le format de l'URL est incorrect.",
    ],

    'date' => [
        'min' => "La date doit être plus supérieure à \":min\".",
        'max' => "La date doit être plus inférieur à \":max\".",
    ],

    'line' => [
        'min' => "Veuillez entrer au moins :min lignes.",
        'max' => "Veuillez entrer au maximum :max lignes.",
    ],

    'file' => [
        'min' => "Veuillez choisir au moins :min fichiers.",
        'max' => "Veuillez choisir au maximum :max fichiers.",
        'max_size' => "\":name\" est trop volumineux (max :size).",
        'min_size' => "\":name\" n'est pas assez volumineux (min :size).",
        'mime_types' => "Le type \":mime\" n'est pas accepté pour le fichier \":name\".",
        'max_width' => "\":name\" est trop large (max :width).",
        'min_width' => "\":name\" n'est pas large (min :width).",
        'max_height' => "\":name\" est trop haute (max :height).",
        'min_height' => "\":name\" n'est pas assez haute (min :height).",
    ],

    'captcha' => [
        'invalid' => "Le captcha n'est pas valide",
    ],

];