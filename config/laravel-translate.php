<?php

use Twigger\Translate\Translate\TranslationManager;

return [
    /*
     * The default driver to use
     *
     * Possible options may include
     * - null: Don't translate any text automatically
     * - aws: Use the AWS Translate service
     * - google-translate-free: Use the free Google Translate service (limited)
     * - deepl: Use DeepL
     */
    'default' => 'null',

    /*
     * Possible configurations
     *
     * Each configuration must have a unique name. They must specify a driver using the notation
     * ```[TranslationManager::DRIVER_KEY => 'null]'```
     * and any configuration for the driver should be added to the array too.
     */
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
            'log_errors' => true
        ],
        'google-translate-free' => [
            TranslationManager::DRIVER_KEY => 'google-translate-free',
            'log_errors' => true
        ],
        'deepl' => [
            TranslationManager::DRIVER_KEY => 'deepl',
            'auth_key' => env('DEEPL_AUTH_KEY'),
            'api_version' => 2,
            'host' => 'api.deepl.com',
            'log_errors' => true
        ]
    ],

    /*
     * The table to save translations in
     */
    'table' => 'translations',

    /**
     * List of languages supported. A user cannot translate to a language not in this list.
     *
     * Leave as an empty array to support all languages.
     */
    'supported_languages' => [],

    /**
     * Configuration for the Detect functionality
     */
    'detection' => [
        /*
         * The key in the request holding the target language
         */
        'body_key' => 'language',

        /*
         * The key of the cookie holding the target language
         */
        'cookie_key' => 'language',

        /*
         * Config for detecting the target language using the header
         */
        'header' => [

            /*
             * ISO-639-1 language codes that are allowed
             */
            'allowed_languages' => []
        ]
    ],

    /*
     * The URL at which the translation API is found
     */
    'translate_api_url' => '_translate'

];
