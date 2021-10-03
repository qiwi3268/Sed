<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Files;

use Throwable;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\Api\Files\UploadRequest;
use App\Http\Controllers\Controller;
use App\Models\Files\File;
use App\Lib\Filesystem\PrivateStorage\StarPath;
use App\Lib\Filesystem\PrivateStorage\PrivateStorageManager;
use App\Services\Files\FileWrapper;
use App\Services\Files\ResponseSerialization\UploadableFileSerializer;


final class FileUploadController extends Controller
{

    /**
     * @throws Throwable
     */
    public function upload(UploadRequest $request, PrivateStorageManager $storage): JsonResponse
    {
        /** @var File[] $preparedFiles */
        $preparedFiles = [];

        foreach ($request->checkedFiles as $file) {

            [$dir, $hashName] = $storage->putFileAsFreeHashName($file);

            $preparedFiles[] = new File([
                'user_id'       => $request->user()->id,
                'original_name' => $file->getClientOriginalName(),
                'size'          => $file->getSize(),
                'file_mapping'  => $request->mappingData->getMapping(),
                'star_path'     => StarPath::createString($dir, $hashName),
            ]);
        }

        $fileWrappers = DB::transaction(function () use ($request, $preparedFiles) {

            $uploader = $request->mappingData->createFileUploader();
            $requestInput = $request->input();

            return array_map(function (File $f) use ($uploader, $requestInput) {
                $f->save();
                return $uploader->processingToDatabase(new FileWrapper($f), $requestInput);
            }, $preparedFiles);
        });

        return success_response(data:
            array_map(fn (FileWrapper $f): UploadableFileSerializer => new UploadableFileSerializer($f), $fileWrappers)
        );
    }
}
