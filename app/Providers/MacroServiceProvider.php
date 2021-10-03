<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\LazyCollection;
use App\Models\User;
use App\Services\Users\CompanyUsersComparator;


final class MacroServiceProvider extends ServiceProvider
{

    public function boot(): void
    {

        /**
         * Возвращает новую коллекцию с пользователями, которые отсортированы по
         * правилам сортировки пользователей учреждения
         */
        EloquentCollection::macro('sortCompanyUsers', function (): EloquentCollection {

            /** @var EloquentCollection $this */

            $comparator = app(CompanyUsersComparator::class);

            return $this->sort(function (User $u1, User $u2) use ($comparator): int {
                return $comparator->compare($u1->last_name, $u2->last_name);
            });
        });


        LazyCollection::macro('sortCompanyUsers', function (): LazyCollection {

            /** @var LazyCollection $this */

            $comparator = app(CompanyUsersComparator::class);

            return $this->sort(function (User $u1, User $u2) use ($comparator): int {
                return $comparator->compare($u1->last_name, $u2->last_name);
            });
        });
    }
}
