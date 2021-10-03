<?php

declare(strict_types=1);

namespace App\Providers;

use LogicException;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use App\Models\ProgramEntity;


final class MorphMapServiceProvider extends ServiceProvider
{

    public const CACHE_KEY = 'morph_map';


    /**
     * Загружает morphMap, которая используется для установки полиморфных отношений
     *
     * @throws LogicException
     */
    public function boot(): void
    {
        $map = Cache::remember(self::CACHE_KEY, now()->addDays(7), function (): array {

            $preMap =  ProgramEntity::select(['id', 'class_name'])->cursor();

            $map = [];

            foreach ($preMap as ['id' => $alias, 'class_name' => $className]) {

                if (!class_exists($className)) {
                    throw new LogicException("В таблице program_entities указан несуществующий класс: '$className'");
                }
                $map[$alias] = $className;
            }
            return $map;
        });

        Relation::morphMap($map);
    }
}
