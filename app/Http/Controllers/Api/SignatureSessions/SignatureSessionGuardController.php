<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\SignatureSessions;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SignatureSessions\Guards\Request;
use App\Repositories\SignatureSessions\SignatureSessionRepository;


final class SignatureSessionGuardController extends Controller
{

    public function canUserSign(Request $request, SignatureSessionRepository $repository): JsonResponse
    {
        return success_response(data: [
            'can' => $repository->canUserSign($request->userUuid, $request->signatureSessionUuid)
        ]);
    }


    public function canUserDelete(Request $request, SignatureSessionRepository $repository): JsonResponse
    {
        return success_response(data: [
            'can' => $repository->canUserDelete($request->userUuid, $request->signatureSessionUuid)
        ]);
    }
}
