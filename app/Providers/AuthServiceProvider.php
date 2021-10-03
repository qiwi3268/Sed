<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Models\SignatureSessions\SignatureSession;
use App\Models\Conferences\Conference;
use App\Policies\SignatureSessionPolicy;
use App\Policies\ConferencePolicy;
use App\Lib\Permissions\Permissions;


class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        SignatureSession::class => SignatureSessionPolicy::class,
        Conference::class => ConferencePolicy::class
    ];


    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define(Permissions::LIST['manage_vacations'], function (User $user): Response {
            return $user->getPermissions()->has(Permissions::LIST['manage_vacations'])
                ? Response::allow()
                : Response::deny('Пользователь не может управлять отпусками');
        });

        Gate::define('create_conferences', function (User $user): Response {
            return $user->getPermissions()->has(Permissions::LIST['manage_conferences'])
                ? Response::allow()
                : Response::deny('Пользователь не может создавать совещания');
        });
    }
}
