<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Miscs;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Miscs\SingleMiscRequest;
use App\Models\Miscs\MiscModel;
use App\Lib\Miscs\SingleMiscManager;
use App\Services\Forms\MiscPresentation;


final class SingleMiscController extends Controller
{

    public function show(SingleMiscRequest $request, SingleMiscManager $manager): JsonResponse
    {
        /** @var MiscModel $misc */
        $misc = new ($manager->getClassNameByAlias($request->alias));

        $collection = $misc
            ->where('is_active', '=', true)
            ->orderBy('sort')
            ->cursor();

        return success_response(data: MiscPresentation::toResponse($collection));
    }
}
