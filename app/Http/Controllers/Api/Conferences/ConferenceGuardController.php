<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Conferences;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Guards\BaseGuardRequest;
use App\Http\Requests\Api\Conferences\Guards\Request;
use App\Models\User;
use App\Models\Conferences\Conference;
use App\Services\RequestValidation\ModelContainer;
use App\Policies\ConferencePolicy;


final class ConferenceGuardController extends Controller
{
    public function __construct(private ModelContainer $modelContainer)
    {}


    public function canUserCreate(BaseGuardRequest $request): JsonResponse
    {
        return success_response(data: [
            'can' => Gate::forUser(
                $this->modelContainer->get(User::class, $request->userUuid)
            )->allows('create_conferences')
        ]);
    }


    public function canUserUpdate(Request $request): JsonResponse
    {
        return success_response(data: [
            'can' => (new ConferencePolicy)->update(
                $this->modelContainer->get(User::class, $request->userUuid),
                $this->modelContainer->get(Conference::class, $request->conferenceUuid)
            )->allowed()
        ]);
    }


    public function canUserDelete(Request $request): JsonResponse
    {
        return success_response(data: [
            'can' => (new ConferencePolicy)->delete(
                $this->modelContainer->get(User::class, $request->userUuid),
                $this->modelContainer->get(Conference::class, $request->conferenceUuid)
            )->allowed()
        ]);
    }
}
