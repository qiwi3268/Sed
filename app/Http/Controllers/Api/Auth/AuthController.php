<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use LogicException;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Hashing\Hasher;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\JWTGuard;


final class AuthController extends Controller
{
    private JWTGuard $guard;


    /**
     * @throws LogicException
     */
    public function __construct(private Hasher $hasher, Guard $guard)
    {
        // Строгая проверка на используемый guard, т.к. далее
        // используются методы, доступные только в JWTGuard
        if (!($guard instanceof JWTGuard)) {
            throw new LogicException("guard не является типом JWTGuard");
        }
        $this->guard = $guard;
    }


    /**
     * Логин пользователя
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string']
        ]);

        if ($this->guard->attempt($credentials, false)) {

            /** @var User $user */
            $user = $this->guard->getLastAttempted();

            // Обновление пароля, если применены новые настройки хэширования
            if ($this->hasher->needsRehash($user->password)) {
                $user->update(['password' => $this->hasher->make($credentials['password'])]);
            }

            $token = $this->guard->login($user);
            return $this->respondWithToken($token);
        }
        return error_response('Неправильный логин или пароль', status: Response::HTTP_UNAUTHORIZED);
    }


    /**
     * Логаут пользователя
     */
    public function logout(): JsonResponse
    {
        // Занесение токена в черный список
        $this->guard->logout(true);
        return success_response();
    }


    /**
     * Возвращает аутентифицированного пользователя
     */
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = $this->guard->user();

        return success_response(data: [
            'uuid'        => $user->uuid,
            'last_name'   => $user->last_name,
            'first_name'  => $user->first_name,
            'middle_name' => $user->middle_name
        ]);
    }


    /**
     * Обновление JWT токена
     */
    public function refresh(): JsonResponse
    {
        // Занесение старого токена в черный список
        // Сброс заявок (claims) для нового токена
        $newToken = $this->guard->refresh(false, true);
        return $this->respondWithToken($newToken);
    }


    /**
     * JWT ответ
     */
    private function respondWithToken(string $token): JsonResponse
    {
        // Переопределение типа, т.к. внутри JWTGuard через __call
        // вызывается метод JWT

        /** @var JWT $jwt */
        $jwt = $this->guard;

        return success_response(data: [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $jwt->factory()->getTTL() * 60 // мин -> сек
        ]);
    }
}
