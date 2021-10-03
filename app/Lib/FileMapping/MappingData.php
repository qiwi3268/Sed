<?php

declare(strict_types=1);

namespace App\Lib\FileMapping;

use App\Services\Files\Uploading\Rules\Rule;
use App\Services\Files\Uploading\Uploaders\FileUploader;
use Webmozart\Assert\Assert;


final class MappingData
{

    public function __construct(
        private string $mapping,
        private string $rule,
        private string $uploader,
        private string $entity
    ) {
        Assert::notEmpty($mapping);
        Assert::allClassExists([$rule, $uploader, $entity]);
    }

    public function getMapping(): string { return $this->mapping; }

    public function createRule(): Rule { return new $this->rule; }

    public function createFileUploader(): FileUploader { return new $this->uploader; }

    public function getEntity(): string { return $this->entity; }
}
