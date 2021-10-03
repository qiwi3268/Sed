<?php

declare(strict_types=1);

namespace App\Rules\Files;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use App\Rules\Traits\ErrorDescription;
use Webmozart\Assert\Assert;


final class AllowableExtensions implements Rule
{
    use ErrorDescription;

    private const SIG_EXTENSIONS = ['sig', 'p7z'];


    public function __construct(private array $allowableExtensions)
    {
        Assert::notEmpty($allowableExtensions);
        Assert::allString($allowableExtensions);
    }


    /**
     * @param $value UploadedFile
     */
    public function passes($attribute, $value): bool
    {
        $name = $value->getClientOriginalName();

        $count = Str::of($name)
            ->lower()
            ->explode('.')
            ->filter(
                // Фильтруются расширения эцп и проверяются допустимые
                fn (string $s): bool => !in_array($s, self::SIG_EXTENSIONS) && in_array($s, $this->allowableExtensions)
            )
            ->count();

        return $this->handle($count == 1, $name);
    }


    public function message(): string
    {
        return "Файл: '$this->description' имеет недопустимое расширение. Допустимые расширения: '" . implode(', ', $this->allowableExtensions) . "'";
    }
}
