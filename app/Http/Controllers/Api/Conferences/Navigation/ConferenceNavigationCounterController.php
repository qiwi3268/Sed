<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Conferences\Navigation;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\Conferences\Navigation\ConferenceNavigationRepository;


final class ConferenceNavigationCounterController extends Controller
{
    public function __construct(
        private ConferenceNavigationRepository $repository
    ) {}


    public function myTodays(Request $request): JsonResponse
    {
        return success_response(data: [
            'count' => $this->repository->getTodaysCount($request->user()->id)
        ]);
    }


    public function myPlanned(Request $request): JsonResponse
    {
        return success_response(data: [
            'count' => $this->repository->getPlannedCount($request->user()->id)
        ]);
    }
}
