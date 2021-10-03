<?php

declare(strict_types=1);

namespace App\Rules\Files\Csp;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Files\File;
use App\Models\Files\FileExternalSignature;
use App\Services\RequestValidation\ModelContainer;
use Webmozart\Assert\Assert;


final class NotRedValidationResult implements Rule
{

    public const INTERNAL_SIGNATURE = 'internal_signature';
    public const EXTERNAL_SIGNATURE = 'external_signature';


    public function __construct(private string $signatureType)
    {
        Assert::inArray($signatureType, [self::INTERNAL_SIGNATURE, self::EXTERNAL_SIGNATURE]);
    }


    public function passes($attribute, $value): bool
    {
        $starPath = (string) $value;

        return !$this->getCheckedModel($starPath)
            ->validation_result
            ->isRedResult();
    }


    /**
     * Возвращает проверяемую модель
     *
     * Устанавливает модель в ModelContainer, если её там нет
     */
    private function getCheckedModel(string $starPath): File|FileExternalSignature
    {
        if ($this->signatureType == self::EXTERNAL_SIGNATURE) {

            return $this->resolveModel(
                FileExternalSignature::class,
                $starPath,
                fn (): FileExternalSignature => FileExternalSignature::whereStarPath($starPath)->firstOrFail()
            );
        } else { // self::INTERNAL_SIGNATURE

            return $this->resolveModel(
                File::class,
                $starPath,
                fn (): File => File::whereStarPath($starPath)->firstOrFail()
            );
        }
    }


    private function resolveModel(string $className, string $starPath, callable $callback): File|FileExternalSignature
    {
        $modelContainer = app(ModelContainer::class);

        if ($modelContainer->has($className, $starPath)) {
            return $modelContainer->get($className, $starPath);
        }

        $model = $callback();

        $modelContainer->set($model, $starPath);

        return $model;
    }


    public function message(): string
    {
        return 'Результат проверки подписи не прошел проверку';
    }
}
