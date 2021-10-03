<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use App\Models\User;
use Closure;


final class UserLastActivity
{
    public function __construct(private Guard $guard)
    {}

    public function handle(Request $request, Closure $next): mixed
    {
        if ($this->guard->check()) {
            /** @var User $user */
            $user = $this->guard->user();
            $user->updateLastActivity();
        }

        return $next($request);
    }
}
