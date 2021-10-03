<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\SignatureSessions\Navigation;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\Api\Navigation\BaseNavigationRequest;
use App\Http\Controllers\Controller;
use App\Repositories\SignatureSessions\Navigation\SignatureSessionNavigationRepository;
use App\Services\Pagination\LengthAwarePaginatorFactory;
use App\Services\Pagination\ItemsMethod;


final class SignatureSessionNavigationItemController extends Controller
{
    public function __construct(
        private SignatureSessionNavigationRepository $repository,
        private LengthAwarePaginatorFactory $factory
    ) {}


    public function waitingAction(BaseNavigationRequest $request): JsonResponse
    {
        return success_response(data:
            $this->factory->create(
                $this->repository->getWaitingActionCount($userId = $request->user()->id),
                new ItemsMethod([$this->repository, 'getWaitingAction'], [$userId]),
                $request->page,
                'signatureSession.navigation.waitingAction'
            )
        );
    }


    public function inWork(BaseNavigationRequest $request): JsonResponse
    {
        return success_response(data:
            $this->factory->create(
                $this->repository->getInWorkCount($userId = $request->user()->id),
                new ItemsMethod([$this->repository, 'getInWork'], [$userId]),
                $request->page,
                'signatureSession.navigation.inWork'
            )
        );
    }


    public function finished(BaseNavigationRequest $request): JsonResponse
    {
        return success_response(data:
            $this->factory->create(
                $this->repository->getFinishedCount($userId = $request->user()->id),
                new ItemsMethod([$this->repository, 'getFinished'], [$userId]),
                $request->page,
                'signatureSession.navigation.finished'
            )
        );
    }
}
