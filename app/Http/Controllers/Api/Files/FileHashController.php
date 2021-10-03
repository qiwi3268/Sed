<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Files;

use Illuminate\Http\JsonResponse;
use Illuminate\Filesystem\FilesystemManager;
use App\Http\Requests\Api\Files\HashRequest;
use App\Http\Controllers\Controller;
use App\Lib\Csp\Hashing\Commands\HashCommand;
use App\Lib\Csp\Hashing\HashGetter;
use App\Lib\Csp\AlgorithmsList;
use App\Lib\Filesystem\PrivateStorage\StarPath;


final class FileHashController extends Controller
{

    public function hash(HashRequest $request, FilesystemManager $filesystemManager): JsonResponse
    {
        $hashDir = $filesystemManager->disk('tmp')->path('');
        $hashAlg = AlgorithmsList::HASH_ALGORITHMS[$request->signatureAlgorithm];

        $file = (new StarPath($request->starPath))->getAbsolutePath();

        $command = new HashCommand($hashDir, $hashAlg, $file);
        $getter = new HashGetter($command);

        return success_response(data: $getter->getHash());
    }
}
