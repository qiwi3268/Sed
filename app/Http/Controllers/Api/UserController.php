<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\Forms\MiscPresentation;


final class UserController extends Controller
{

    public function index(UserRepository $repository): JsonResponse
    {
        return success_response(
            data: MiscPresentation::toResponse($repository->getToMiscPresentation())
        );
    }
}
