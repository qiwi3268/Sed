<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\SignatureSessions\Navigation;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\SignatureSessions\Navigation\SignatureSessionNavigationRepository;


final class SignatureSessionNavigationCounterController extends Controller
{
    public function __construct(
        private SignatureSessionNavigationRepository $repository
    ) {}


    public function waitingAction(Request $request): JsonResponse
    {
        return success_response(data: [
            'count' => $this->repository->getWaitingActionCount($request->user()->id)
        ]);
    }


    public function inWork(Request $request): JsonResponse
    {
        return success_response(data: [
            'count' => $this->repository->getInWorkCount($request->user()->id)
        ]);
    }


    public function finished(Request $request): JsonResponse
    {
        return success_response(data: [
            'count' => $this->repository->getFinishedCount($request->user()->id)
        ]);
    }
}
