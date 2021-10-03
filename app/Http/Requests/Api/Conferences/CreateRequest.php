<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Conferences;

use App\Http\Requests\AppFormRequest;
use App\Models\User;
use App\Rules\Miscs\FullMisc;
use DateTimeImmutable;


/**
 * @property-read string $topic
 * @property-read DateTimeImmutable $startAt
 * @property-read int $conferenceForm
 * @property-read ?string $vksHref
 * @property-read ?int $vksConnectionResponsibleId
 * @property-read int $conferenceLocation
 * @property-read int[] $memberIds
 * @property-read ?string $outerMembers
 * @property-read ?string $comment
 */
class CreateRequest extends AppFormRequest
{

    public function rules(): array
    {
        return [
            'topic'   => ['bail', 'required', 'string', 'max:200'],
            'startAt' => [
                'bail',
                'required',
                'date_format:Y-m-d H:i',
                'after_or_equal:now'
            ],
            'conferenceForm' => ['bail', 'required', 'integer', new FullMisc()],
            'vksHref'        => [
                'bail',
                'present',
                'nullable',
                'string',
                'url',
                'max:400'
            ],
            'vksConnectionResponsibleId' => [
                'bail',
                'present',
                'nullable',
                'integer',
                'exists:' . User::class . ',id'
            ],
            'conferenceLocation' => ['bail', 'required', 'integer', new FullMisc()],
            'memberIds'   => ['required', 'array'],
            'memberIds.*' => [
                'bail',
                'required',
                'integer',
                'distinct',
                'exists:' . User::class . ',id'
            ],
            'outerMembers' => ['bail', 'present', 'nullable', 'string', 'max:500'],
            'comment'      => ['bail', 'present', 'nullable', 'string', 'max:500'],
        ];
    }


    protected function mutateInputValue(string $key, mixed &$value): void
    {
        switch ($key) {
            case 'startAt':
                $value = resolve_date($value);
                break;
            case 'conferenceForm':
            case 'conferenceLocation':
                $value = (int) $value;
                break;
            case 'vksConnectionResponsibleId':
                if (is_string($value)) {
                    $value = (int) $value;
                }
                break;
            case 'memberIds':
                $value = arr_to_int($value);
                break;
        }
    }
}
