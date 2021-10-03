<?php

declare(strict_types=1);

namespace Tests\Unit\DateShifter;

use Exception;
use App\Lib\DateShifter\Exceptions\CalendarCreationException;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use App\Lib\DateShifter\Calendar\CalendarCachingFactory;
use App\Lib\DateShifter\Calendar\Calendar;
use App\Lib\DateShifter\Calendar\CalendarFactory;

class CalendarCachingFactoryTest extends TestCase
{
    private CacheInterface $cache;

    private CalendarFactory $calendarFactory;
    private Calendar $calendar;
    private int $ttl = 600;


    public function setUp(): void
    {
        $this->cache = $this->createMock(CacheInterface::class);
        $this->calendarFactory = new FakeScheduleCalendarFactory();
        $this->calendar = $this->calendarFactory->create();
    }


    public function testCreateWhenCacheMissed(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with(CalendarCachingFactory::CACHE_KEY);

        $this->cache->expects($this->once())
            ->method('set')
            ->with(CalendarCachingFactory::CACHE_KEY, serialize($this->calendar), $this->ttl);

        $calendarCachingFactory = new CalendarCachingFactory($this->calendarFactory, $this->cache, $this->ttl);

        $this->assertEquals($this->calendar, $calendarCachingFactory->create());
    }


    public function testCreateWhenCacheHit(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with(CalendarCachingFactory::CACHE_KEY)
            ->willReturn(serialize($this->calendar));

        $this->cache->expects($this->never())->method('set');

        $calendarCachingFactory = new CalendarCachingFactory($this->calendarFactory, $this->cache, $this->ttl);

        $this->assertEquals($this->calendar, $calendarCachingFactory->create());
    }


    public function testDeleteCache(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('delete')
            ->with(CalendarCachingFactory::CACHE_KEY);

        (new CalendarCachingFactory($this->calendarFactory, $this->cache, $this->ttl))->deleteCache();
    }


    public function testCreateException(): void
    {
        $this->cache
            ->method('get')
            ->willThrowException(new Exception('internal exception'));

        $this->expectException(CalendarCreationException::class);

        (new CalendarCachingFactory($this->calendarFactory, $this->cache, $this->ttl))->create();
    }


    public function testInvalidSerializedValue(): void
    {
        $this->cache
            ->method('get')
            ->willReturn('invalid');

        $this->expectException(CalendarCreationException::class);
        $this->expectExceptionMessage("Ошибка при десериализации строки: 'invalid'");

        (new CalendarCachingFactory($this->calendarFactory, $this->cache, $this->ttl))->create();
    }


    public function testNotACalendarObjectSerializedValue(): void
    {
        $object = (object) ['a' => 1, 'b' => 2];

        $this->cache
            ->method('get')
            ->willReturn(serialize($object));

        $this->expectException(CalendarCreationException::class);
        $this->expectExceptionMessage('Десериализованные данные не является объектом Calendar');

        (new CalendarCachingFactory($this->calendarFactory, $this->cache, $this->ttl))->create();
    }
}
