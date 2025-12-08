<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'ayet_studios' => [
        'app_key' => env('AYET_STUDIOS_APP_KEY'),
        'identifier' => env('AYET_STUDIOS_IDENTIFIER'),
        'adslot' => env('AYET_STUDIOS_ADSLOT'),
        'url' => 'https://www.ayetstudios.com',
    ],

    'adgem' => [
        'api_key' => env('ADGEM_API_KEY'),
        'secret' => env('ADGEM_SECRET'),
        'url' => 'https://offer-api.adgem.com',
    ],

    'cpx' => [
        'app_id' => env('CPX_API_ID'),
        'hash' => env('CPX_API_HASH'),
        'url' => 'https://live-api.cpx-research.com/api',
    ],
];
