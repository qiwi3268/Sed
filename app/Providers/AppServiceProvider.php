<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {

    }


    public function boot(): void
    {
        $scheme = request()->header('x-forwarded-proto');

        if (!is_null($scheme)) {
            url()->forceScheme($scheme);
        }
    }
}
