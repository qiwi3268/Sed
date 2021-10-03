<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Files\SignatureValidation;

use Throwable;
use App\Lib\Csp\Exceptions\NoSignersException;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Files\File;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Files\SignatureValidation\ExternalSignatureValidationRequest;
use App\Lib\Filesystem\PrivateStorage\StarPath;
use App\Lib\Filesystem\PrivateStorage\PrivateStorageManager;
use App\Lib\Csp\SignatureValidation\SignatureValidator;
use App\Lib\Csp\SignatureValidation\Commands\ExternalSignatureValidationCommand;
use App\Lib\Csp\SignatureValidation\OutputMessage\CommandLineOutputMessenger;
use App\Lib\Csp\SignatureValidation\Entities\ValidationResult;
use App\Services\RequestValidation\ModelContainer;
use App\Services\Files\ResponseSerialization\ValidationResultSerializer;


/**
 * В контроллере выполняется загрузка файла открепленной подписи и её проверка
 */
final class ExternalSignatureValidationController extends Controller
{

    /**
     * @throws Throwable
     */
    public function validateSignature(
        ExternalSignatureValidationRequest $request,
        PrivateStorageManager $storage,
        ModelContainer $modelContainer,
    ): JsonResponse {

        [$dir, $hashName] = $storage->putFileAsFreeHashName($request->file('file'));

        $originalStarPath = new StarPath($request->originalStarPath);
        $externalSignatureStarPath = StarPath::create($dir, $hashName);

        $command = new ExternalSignatureValidationCommand(
            $originalStarPath->getAbsolutePath(),
            $externalSignatureStarPath->getAbsolutePath()
        );
        $messenger = new CommandLineOutputMessenger($command);
        $validator = new SignatureValidator($messenger);

        try {
            $validationResult = $validator->validate();
        } catch (NoSignersException) {
            return error_response('Некорректная подпись' , code: ErrorCodes::NO_SIGNERS_ERROR_CODE);
        }

        $originalFile = $modelContainer->get(File::class, $request->originalStarPath);

        DB::transaction(function () use (
            $originalFile,
            $request,
            $validationResult,
            $externalSignatureStarPath
        ): void {

            $originalFile->externalSignatureFiles()->create([
                'user_id'           => $request->user()->id,
                'star_path'         => (string) $externalSignatureStarPath,
                'validation_result' => $validationResult,
                'created_name'      => $this->createFileName($validationResult),
                'size'              => $request->file('file')->getSize()
            ]);
        });

        return success_response(data: [
            'starPath'         => (string) $externalSignatureStarPath,
            'validationResult' => new ValidationResultSerializer($validationResult)
        ]);
    }


    /**
     * Создаёт имя файла на основе результата проверки подписи
     */
    private function createFileName(ValidationResult $validationResult): string
    {
        $shortNames = [];

        foreach ($validationResult->getSigners() as $signer) {
            $shortNames[] = $signer->getCertificate()->getSubjectFio()->getShortFio();
        }

        return implode(' ', $shortNames) . '.sig';
    }
}
