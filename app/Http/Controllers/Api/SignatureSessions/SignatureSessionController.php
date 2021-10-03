<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\SignatureSessions;

use Throwable;
use Illuminate\Auth\Access\AuthorizationException;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Http\Requests\Api\SignatureSessions\CreateRequest;
use App\Http\Requests\Api\SignatureSessions\DeleteRequest;
use App\Http\Requests\Api\SignatureSessions\ShowRequest;
use App\Http\Requests\Api\SignatureSessions\CreateSignRequest;
use App\Http\Controllers\Controller;
use App\Models\SignatureSessions\SignatureSession;
use App\Models\Files\File;
use App\Models\Files\FileExternalSignature;
use App\Repositories\SignatureSessions\SignatureSessionRepository;
use App\Services\RequestValidation\ModelContainer;
use App\Events\SignatureSessions\SignatureSessionCreated;
use App\Events\SignatureSessions\SignatureSessionFinished;


final class SignatureSessionController extends Controller
{

    /**
     * @throws Throwable
     */
    public function create(
        CreateRequest $request,
        ModelContainer $modelContainer,
    ): JsonResponse {

        $file = $modelContainer->get(File::class, $request->originalStarPath);

        $signerAssignments = array_map(fn (int $id) => ['signer_id' => $id], $request->signerIds);

        $signatureSession = DB::transaction(function () use (
            $request,
            $file,
            $signerAssignments
        ): SignatureSession {

            $signatureSession = SignatureSession::create([
                'uuid'      => Str::uuid()->toString(),
                'author_id' => $request->user()->id,
                'title'     => $request->title,
                'file_id'   => $file->id,
            ]);

            $file->associateToProgramEntity($signatureSession);

            $signatureSession->signerAssignments()->createMany($signerAssignments);

            return $signatureSession;
        });

        SignatureSessionCreated::dispatch($signatureSession);

        return success_response();
    }


    /**
     * @throws AuthorizationException
     */
    public function delete(
        DeleteRequest $request,
        ModelContainer $modelContainer
    ): JsonResponse {

        $signatureSession = $modelContainer->get(SignatureSession::class, $request->signatureSessionUuid);

        $this->authorize('delete', $signatureSession);

        $signatureSession->delete();

        return success_response();
    }


    public function show(ShowRequest $request, SignatureSessionRepository $repository): JsonResponse
    {
        return success_response(data: $repository->get($request->signatureSessionUuid));
    }


    public function showSigning(ShowRequest $request, SignatureSessionRepository $repository): JsonResponse
    {
        return success_response(data: $repository->getForSigning($request->signatureSessionUuid));
    }


    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function createSign(
        CreateSignRequest $request,
        SignatureSessionRepository $repository,
        ModelContainer $modelContainer
    ): JsonResponse {

        $signatureSession = $modelContainer->get(SignatureSession::class, $request->signatureSessionUuid);

        $this->authorize('createSign', $signatureSession);

        $fileExternalSignature = $modelContainer->get(FileExternalSignature::class, $request->externalSignatureStarPath);

        $signerAssignment = $signatureSession->signerAssignment($request->user()->id);

        $finished = DB::transaction(function () use (
            $request,
            $signatureSession,
            $signerAssignment,
            $fileExternalSignature,
            $repository
        ): bool {

            $signerAssignment
                ->sign()
                ->signedFile()->create([
                    'file_external_signature_id' => $fileExternalSignature->id
                ]);

            $fileExternalSignature->setNeeds()->save();

            // Завершение сессии подписания
            if ($repository->isAllSigned($request->signatureSessionUuid)) {
                $signatureSession->finish();
                $finished = true;
            } else {
                $finished = false;
            }
            return $finished;
        });

        SignatureSessionFinished::dispatchIf($finished, $signatureSession);

        return success_response();
    }
}
