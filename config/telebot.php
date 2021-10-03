<?php

use App\Services\Telegram\CompanyTelegram;

return [
    /*-------------------------------------------------------------------------
    | Default Bot Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the bots you wish to use as
    | your default bot for regular use.
    |
    */

    'default' => CompanyTelegram::COMPANY_MAIN_BOT,

    /*-------------------------------------------------------------------------
    | Your Telegram Bots
    |--------------------------------------------------------------------------
    | You may use multiple bots. Each bot that you own should be configured here.
    |
    | See the docs for parameters specification:
    | https://westacks.github.io/telebot/#/configuration
    |
    */

    'bots' => [
        CompanyTelegram::COMPANY_MAIN_BOT => [
            'token' => config('services.company_telegram.main_bot.token'),
            'name' => config('services.company_telegram.main_bot.username'),
            'api_url' => 'https://api.telegram.org',
            'exceptions' => true,
            'async' => false,

            'webhook' => [
                // 'url'               => env('TELEGRAM_BOT_WEBHOOK_URL', env('APP_URL').'/telebot/webhook/bot/'.env('TELEGRAM_BOT_TOKEN')),
                // 'certificate'       => env('TELEGRAM_BOT_CERT_PATH', storage_path('app/ssl/public.pem')),
                // 'ip_address'        => '8.8.8.8',
                // 'max_connections'   => 40,
                // 'allowed_updates'   => ["message", "edited_channel_post", "callback_query"]
            ],

            'poll' => [
                // 'limit'             => 100,
                // 'timeout'           => 0,
                // 'allowed_updates'   => ["message", "edited_channel_post", "callback_query"]
            ],

            'handlers' => [

                // updates

                \App\Services\Telegram\Handlers\Updates\AtWorkPollUpdateHandler::class,

                // commands

                \App\Services\Telegram\Handlers\Commands\CreateAccountCommandHandler::class
            ],
        ],

        // 'second_bot' => [
        //     'token'         => env('TELEGRAM_BOT2_TOKEN', '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11'),
        // ],
    ],
];
