<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Files;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Requests\AppFormRequest;
use App\Lib\FileMapping\MappingCollection;
use App\Lib\FileMapping\MappingData;
use App\Rules\FileMapping;
use App\Rules\Files\AllowableExtensions;
use App\Rules\Files\ForbiddenSymbols;


final class UploadRequest extends AppFormRequest
{
    /**
     * @var UploadedFile[]
     */
    public array $checkedFiles;
    public MappingData $mappingData;


    public function rules(Request $request, MappingCollection $mappings): array
    {
        // Общие правила api
        //
        $this->stoppingValidate([
            'mapping'    => ['bail', 'required', 'string', new FileMapping()],
            'files'      => ['required', 'array'],
            // Успешно загруженный файл на сервер
            // Файл не пустой (больше 1 Кб)
            'files.*'    => ['bail', 'required', 'file', 'min:1']
        ]);

        $result = [];
        $result['files.*'] = ['bail', 'file'];

        $mappingData = $mappings->get($request->get('mapping'));

        // Правила маппинга
        //
        $rule = $mappingData->createRule();

        if (!$rule->isMultipleAllowed()) {
            // Проверка на array необходима для валидатора
            $result['files'] = ['bail', 'array', 'max:1'];
        }

        $result['files.*'][] = 'max:' . $rule->getMaxSize() * 1024; // Мб -> Кб

        if (!is_null($allowableExtensions = $rule->getAllowableExtensions())) {
            $result['files.*'][] = new AllowableExtensions($allowableExtensions);
        }
        if (!is_null($forbiddenSymbols = $rule->getForbiddenSymbols())) {
            $result['files.*'][] = new ForbiddenSymbols($forbiddenSymbols);
        }

        // Правила загрузчика
        //
        $uploader = $mappingData->createFileUploader();

        if (!is_null($uploaderRules = $uploader->getValidationRules())) {
            $result = array_merge_recursive($result, $uploaderRules);
        }

        /** @var UploadedFile[] $files */
        $files = $request->file('files');
        $this->checkedFiles = $files;
        $this->mappingData = $mappingData;

        return $result;
    }
}
