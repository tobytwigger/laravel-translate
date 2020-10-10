<?php

use Twigger\Translate\Translate\TranslationManager;

return [
    'default' => 'null',

    'configurations' => [
        'null' => [
            TranslationManager::DRIVER_KEY => 'null'
        ],
        'aws' => [
            TranslationManager::DRIVER_KEY => 'aws',
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION', 'eu-west-2'),
            'key'    => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ]
    ],

    'table' => 'translations',

    'detection_body_key' => 'language',
    'detection_cookie_key' => 'language',

    'detection_header_allowed_languages' => [
        'en',
        'en_GB',
        'fr'
    ],
    'translate_api_url' => '_translate',
    'default_language' => 'en_US'
];
