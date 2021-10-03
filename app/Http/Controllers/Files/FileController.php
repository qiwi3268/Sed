<?php

declare(strict_types=1);

namespace App\Http\Controllers\Files;

use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Files\DownloadRequest;
use App\Models\Files\File;
use App\Lib\Filesystem\PrivateStorage\StarPath;


final class FileController extends Controller
{

    public function download(DownloadRequest $request, ResponseFactory $responseFactory): BinaryFileResponse
    {
        $starPath = new StarPath($request->starPath);
        $name = File::whereStarPath($request->starPath)->firstOrFail(['original_name'])->original_name;

        return $responseFactory->download($starPath->getAbsolutePath(), $name);
    }
}
