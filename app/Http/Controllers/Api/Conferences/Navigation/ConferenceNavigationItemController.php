<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Conferences\Navigation;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Api\Conferences\Navigation\AllForDateRequest;
use App\Http\Controllers\Controller;
use App\Repositories\Conferences\Navigation\ConferenceNavigationRepository;


final class ConferenceNavigationItemController extends Controller
{
    public function __construct(
        private ConferenceNavigationRepository $repository
    ) {}


    public function myTodays(Request $request): JsonResponse
    {
        return success_response(data:
           $this->repository->getTodays($request->user()->id)
        );
    }


    public function myPlanned(Request $request): JsonResponse
    {
        return success_response(data:
            $this->repository->getPlanned($request->user()->id)
        );
    }


    public function allForDate(AllForDateRequest $request): JsonResponse
    {
        return success_response(data:
            $this->repository->getByDate($request->date)
        );
    }
}
