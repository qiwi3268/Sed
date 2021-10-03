<?php

declare(strict_types=1);

namespace App\Services\Users;

use App\Lib\Singles\PriorityStringComparator;


final class CompanyUsersComparator
{
    public function __construct(private PriorityStringComparator $comparator)
    {}


    public function compare(string $lastName1, string $lastName2): int
    {
        return $this->comparator->ascCompare($lastName1, $lastName2);
    }
}
