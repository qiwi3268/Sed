<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Conferences\Navigation;

use Illuminate\Http\JsonResponse;

use App\Http\Requests\Api\Conferences\Navigation\DatesWithConferencesForYearRequest;
use App\Http\Controllers\Controller;
use App\Repositories\Conferences\Navigation\ConferenceNavigationRepository;


final class ConferenceNavigationAllController extends Controller
{

    public function datesWithConferencesForYear(
        DatesWithConferencesForYearRequest $request,
        ConferenceNavigationRepository $repository
    ): JsonResponse {
        return success_response(
            data: $repository->getDatesWithConferencesByYear($request->year)
        );
    }
}
