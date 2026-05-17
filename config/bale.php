<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bale Bot Configurations
    |--------------------------------------------------------------------------
    */
    'bots' => [
        'documentation' => [
            'token' => env('BALE_DOC_BOT_TOKEN'),
            'webhook_url' => env('APP_URL') . 'api/webhook/bale/documentation',
            'name' => 'Documentation Bot',
            'service' => \App\Services\Bale\DocumentationBotService::class,
        ],
        'hafezfal' => [
            'token' => env('BALE_HAFEZ_FAL_BOT_TOKEN'),
            'webhook_url' => env('APP_URL') . 'api/webhook/bale/hafezfal',
            'name' => 'Hafezfal Bot',
            'service' => \App\Services\Bale\HafezFalBotService::class,
        ],
        'resume' => [
            'token' => env('BALE_RESUME_TOKEN'),
            'webhook_url' => env('APP_URL') . 'api/webhook/bale/resume',
            'name' => 'Resume Bot',
            'service' => \App\Services\Bale\DocumentationBotService::class,
        ],
        // می‌توانید ربات‌های دیگر اینجا اضافه کنید
        // 'support' => [
        //     'token' => env('BALE_SUPPORT_BOT_TOKEN'),
        //     'webhook_url' => env('APP_URL') . '/webhook/bale/support',
        //     'name' => 'Support Bot',
        //     'service' => \App\Services\Bale\SupportBotService::class,
        // ],
    ],

    // تنظیمات عمومی
    'timeout' => 30,
    'retry_attempts' => 3,
];
