<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Birthdays;

use Illuminate\Http\JsonResponse;

use App\Http\Controllers\Controller;
use App\Repositories\Birthdays\BirthdayRepository;


final class BirthdayController extends Controller
{

    public function index(BirthdayRepository $repository): JsonResponse
    {
        return success_response(data: $repository->getAll());
    }
}
