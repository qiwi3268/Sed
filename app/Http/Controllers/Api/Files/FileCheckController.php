<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Files;

use App\Services\StarPathValidation\Exceptions\StarPathValidationException;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Files\CheckRequest;
use App\Services\StarPathValidation\StarPathValidator;


final class FileCheckController extends Controller
{

    public function check(CheckRequest $request): JsonResponse
    {
        $validator = new StarPathValidator($request->starPath);

        try {
            $validator
                ->validateSyntax()
                ->validateFilesystem()
                ->validateDatabase();
            return success_response();
        } catch (StarPathValidationException $e) {
            return error_response($e->getMessage());
        }
    }
}
