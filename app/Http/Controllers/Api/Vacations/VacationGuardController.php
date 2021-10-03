<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vacations;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Guards\BaseGuardRequest;
use App\Models\User;
use App\Lib\Permissions\Permissions;
use App\Services\RequestValidation\ModelContainer;


final class VacationGuardController extends Controller
{

    public function canUserManage(
        BaseGuardRequest $request,
        ModelContainer $modelContainer
    ): JsonResponse {

        $user = $modelContainer->get(User::class, $request->userUuid);

        return success_response(data: [
            'can' => Gate::forUser($user)->allows(Permissions::LIST['manage_vacations'])
        ]);
    }
}
