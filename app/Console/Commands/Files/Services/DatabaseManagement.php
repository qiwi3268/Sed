<?php

declare(strict_types=1);

namespace App\Console\Commands\Files\Services;

use Throwable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Files\Services\FileServiceDatabaseManagement;
use App\Repositories\Files\Services\DatabaseManagementRepository;


final class DatabaseManagement extends Command
{

    protected $signature = 'file_service:database_management';

    protected $description = 'Выполняет менеджмент записей файловых таблиц';


    /**
     * @throws Throwable
     */
    public function handle(DatabaseManagementRepository $repository): int
    {
        $attributes = DB::transaction(function() use ($repository): array {

            FileServiceDatabaseManagement::create($attributes = [
                'updated_needs'    => $repository->updateNeeds(),
                'updated_no_needs' => $repository->updateNoNeeds(),
                'deleted_no_needs' => $repository->deleteNoNeeds()
            ]);

            return $attributes;
        });

        $this->info('Результат: ' . assoc_implode($attributes, ': '));
        return 0;
    }
}
