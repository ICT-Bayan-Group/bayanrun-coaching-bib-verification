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
        'token' => env('POSTMARK_TOKEN'),
    ],

     'wablas' => [
        'url' => env('WABLAS_API_URL', 'https://sby.wablas.com/api/send-message'),
        'token' => env('WABLAS_TOKEN'),
        'image_url' => env('WABLAS_IMAGE_URL', 'https://sby.wablas.com/api/send-image'),
    ],

     /*
    |--------------------------------------------------------------------------
    | Mekari Qontak WhatsApp Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Mekari Qontak WhatsApp Business API
    | Documentation: https://docs.qontak.com
    |
    */
    'qontak' => [
        // API Base URL
        'url' => env('QONTAK_API_URL', 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct'),
        
        // Bearer Token untuk autentikasi
        'token' => env('QONTAK_TOKEN'),
        
        // Channel Integration ID dari dashboard Qontak
        'channel_integration_id' => env('QONTAK_CHANNEL_INTEGRATION_ID'),
        
        // Template ID (opsional - untuk menggunakan message template)
        'template_id' => env('QONTAK_TEMPLATE_ID', ''),
        
        // Timeout settings (dalam detik)
        'timeout' => env('QONTAK_TIMEOUT', 45),
        
        // Retry settings
        'max_retries' => env('QONTAK_MAX_RETRIES', 3),
        'retry_delay' => env('QONTAK_RETRY_DELAY', 2), // detik
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

];
