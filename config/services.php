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

    'wms_ai' => [
        'provider' => env('WMS_AI_PROVIDER', 'auto'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'wms_model' => env('OPENAI_WMS_MODEL', 'gpt-5.4-mini'),
        'wms_reasoning_effort' => env('OPENAI_WMS_REASONING_EFFORT', 'low'),
        'wms_analysis_reasoning_effort' => env('OPENAI_WMS_ANALYSIS_REASONING_EFFORT', 'medium'),
        'wms_enable_web_search' => env('OPENAI_WMS_WEB_SEARCH', true),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta/openai'),
        'wms_model' => env('GEMINI_WMS_MODEL', 'gemini-2.5-flash'),
        'wms_analysis_model' => env('GEMINI_WMS_ANALYSIS_MODEL', 'gemini-2.5-pro'),
        'wms_reasoning_effort' => env('GEMINI_WMS_REASONING_EFFORT', 'low'),
        'wms_analysis_reasoning_effort' => env('GEMINI_WMS_ANALYSIS_REASONING_EFFORT', 'medium'),
    ],

];
