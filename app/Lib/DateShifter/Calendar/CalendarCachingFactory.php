<?php

declare(strict_types=1);

namespace App\Lib\DateShifter\Calendar;

use Exception;
use RuntimeException;
use Psr\SimpleCache\CacheException;
use App\Lib\DateShifter\Exceptions\CalendarCreationException;
use Psr\SimpleCache\CacheInterface;

final class CalendarCachingFactory implements CalendarFactory
{
    /**
     * Ключ, по которому сериализованный объект {@see Calendar} будет сохранён в кэш хранилище
     */
    public const CACHE_KEY = 'calendar_caching_factory';

    private CalendarFactory $factory;
    private CacheInterface $cache;
    private ?int $ttl;


    /**
     * @param CalendarFactory $factory другая фабрика, которая будет использована для создания
     * календаря
     * @param CacheInterface $cache кэш хранилище, которое будет содержать календарь
     * в сериализованном виде
     * @param ?int $ttl время жизни календаря в кэш хранилище в секундах.
     * Если null, то будет установлено значение по умолчанию
     */
    public function __construct(
        CalendarFactory $factory,
        CacheInterface $cache,
        ?int $ttl = null
    ) {
        $this->factory = $factory;
        $this->cache = $cache;
        $this->ttl = $ttl;
    }


    /**
     * Фабричный метод по созданию объекта {@see Calendar}
     *
     * Если кэш хранилище уже содержит календарь, то метод просто возвращает его.
     *
     * Если календарь отсутствует в кэш хранилище, то метод создаст его, используя
     * принятую в конструкторе фабрику.
     * Созданный объект будет установлен в кэш хранилище и возвращён пользователю
     *
     * @throws CalendarCreationException в случае ошибки при создании календаря
     */
    public function create(): Calendar
    {
        try {

            $serialized = $this->cache->get(self::CACHE_KEY);

            if (is_null($serialized)) {

                $calendar = $this->factory->create();

                $this->cache->set(self::CACHE_KEY, serialize($calendar), $this->ttl);

                return $calendar;
            }
        } catch (Exception | CacheException $e) {
            throw new CalendarCreationException('Ошибка при создании календаря', 0, $e);
        }

        $calendar = @unserialize($serialized);

        if ($calendar === false) {
            throw new CalendarCreationException("Ошибка при десериализации строки: '$serialized'");
        } elseif (!($calendar instanceof Calendar)) {
            throw new CalendarCreationException('Десериализованные данные не является объектом Calendar');
        }

        return $calendar;
    }


    /**
     * Удаляет календарь из кэш хранилища
     *
     * @throws RuntimeException
     */
    public function deleteCache(): self
    {
        try {
            $this->cache->delete(self::CACHE_KEY);
        } catch (Exception | CacheException $e) {
            throw new RuntimeException('Ошибка при удалении календаря из кэш хранилища', 0, $e);
        }
        return $this;
    }
}
