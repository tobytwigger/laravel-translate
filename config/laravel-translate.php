<?php

use Twigger\Translate\Translate\TranslationManager;

return [
    'default' => 'null',

    'configurations' => [
        'null' => [
            TranslationManager::DRIVER_KEY => 'null'
        ]
    ],

    'table' => 'translations',

    'detection_body_key' => 'language',
    'detection_cookie_key' => 'language',

    'detection_header_allowed_languages' => [
        'en',
        'en_GB',
        'fr'
    ]
];
