<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\LazyCollection;
use App\Models\User;


final class UserRepository
{

    public function getToMiscPresentation(): LazyCollection
    {
        return User::select([
            'id', 'last_name', 'first_name', 'middle_name'
        ])->cursor()->sortCompanyUsers();
    }
}
