<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Files;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Files\RulesRequest;
use App\Lib\FileMapping\MappingCollection;


final class FileRuleController extends Controller
{

    public function rule(RulesRequest $request, MappingCollection $mappings): JsonResponse
    {
        $rule = $mappings->get($request->mapping)->createRule();

        return success_response(data: [
            'multipleAllowed'     => $rule->isMultipleAllowed(),
            'maxSize'             => $rule->getMaxSize(),
            'allowableExtensions' => $rule->getAllowableExtensions(),
            'forbiddenSymbols'    => $rule->getForbiddenSymbols()
        ]);
    }
}
