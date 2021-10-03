<?php

declare(strict_types=1);

namespace App\Models\Traits\Casts;

use LogicException;
use Illuminate\Database\Eloquent\Model;
use App\Lib\Singles\Fio;


trait FioCast
{

    public function setFioAttribute(Fio $fio): void
    {
        /** @var Model $this */
        $this->attributes['last_name']   = $fio->getLastName();
        $this->attributes['first_name']  = $fio->getFirstName();
        $this->attributes['middle_name'] = $fio->getMiddleName();
    }


    /**
     * @throws LogicException
     */
    public function getFioAttribute(): Fio
    {
        /** @var Model $this */
        $attributes = $this->attributes;

        if (!arr_all_key_exists($attributes, [
            'last_name', 'first_name', 'middle_name'
        ])) {
            throw new LogicException('Отсутствуют необходимые атрибуты ФИО');
        }

        return new Fio(
            $attributes['last_name'],
            $attributes['first_name'],
            $attributes['middle_name']
        );
    }
}
