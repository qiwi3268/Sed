<?php

declare(strict_types=1);

namespace App\Http\Controllers\Files\SignatureSessions;

use App\Http\Requests\Files\SignatureSessions\DownloadGeneratedZipRequest;

use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use App\Http\Controllers\Controller;
use App\Models\SignatureSessions\SignatureSession;
use App\Lib\ZipArchive\ZipArchiveCreator;
use App\Services\RequestValidation\ModelContainer;
use App\Services\SignatureSessions\SignatureSessionFileService;


final class FileSignatureSessionController extends Controller
{

    public function downloadGeneratedZip(
        DownloadGeneratedZipRequest $request,
        ResponseFactory $responseFactory,
        ModelContainer $modelContainer,
        SignatureSessionFileService $fileService,
        ZipArchiveCreator $archiveCreator
    ): BinaryFileResponse {

        $fileBag = $fileService->createFileBag($request->signatureSessionUuid);

        $archiveFile = $archiveCreator->createTmpArchive($fileBag);

        $signatureSession = $modelContainer->get(SignatureSession::class, $request->signatureSessionUuid);

        return $responseFactory->download(
            $archiveFile->getPathname(),
            $fileService->createArchiveName($signatureSession->id)
        );
    }
}
