<?php

declare(strict_types=1);

namespace App\Services\Files\Uploading\Uploaders;

use App\Services\Files\FileWrapper;


final class SignatureSessionUploader implements FileUploader
{

    public function getValidationRules(): ?array
    {
        return null;
    }


    public function processingToDatabase(FileWrapper $fileWrapper, array $requestInput): FileWrapper
    {
        return $fileWrapper;
    }


    public function processingFromDatabase(FileWrapper $fileWrapper): FileWrapper
    {
        return $fileWrapper;
    }
}
