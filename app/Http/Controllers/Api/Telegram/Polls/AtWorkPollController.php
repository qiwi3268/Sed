<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Telegram\Polls;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Telegram\Polls\ShowForDateRequest;
use App\Repositories\Telegram\Polls\AtWorkPollRepository;


final class AtWorkPollController extends Controller
{
    public function showForDate(ShowForDateRequest $request, AtWorkPollRepository $repository): JsonResponse
    {
        return success_response(data: $repository->getByDate($request->date));
    }
}
