<?php

declare(strict_types=1);

namespace App\Providers;

use Psr\SimpleCache\CacheInterface;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;

use App\Lib\FileMapping\MappingCollection;
use App\Lib\RouteSynchronization\RouteSynchronizer;
use App\Lib\DateShifter\Calendar\CalendarFactory;
use App\Lib\DateShifter\Calendar\Calendar;
use App\Lib\DateShifter\DateShifter;
use App\Lib\DateShifter\DateCalculator;
use App\Lib\DatabaseMagicNumbers\DatabaseMagicNumbersManager;
use App\Lib\WeatherForecast\WeatherForecast;
use App\Lib\Miscs\SingleMiscManager;
use App\Services\Settings\YamlMappingCollection;
use App\Services\Settings\YamlRouteSynchronizer;
use App\Services\Settings\YamlDatabaseMagicNumbersManager;
use App\Services\RequestValidation\ModelContainer;
use App\Services\Telegram\CompanyTelegram;
use App\Services\WeatherForecast\OpenWeatherMapAdapter;
use App\Services\Settings\YamlSingleMiscManager;
use App\Lib\DateShifter\Calendar\CalendarCachingFactory;
use App\Services\Settings\YamlScheduleCalendarFactory;
use App\Services\Users\CompanyUsersComparator;
use App\Lib\Singles\PriorityStringComparator;
use WeStacks\TeleBot\BotManager;
use GuzzleHttp\Psr7\HttpFactory as GuzzleHttpFactory;
use App\Providers\Utils\ProviderHelper;
use Cmfcmf\OpenWeatherMap;
use GuzzleHttp\Client as GuzzleClient;


final class SingletonServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides(): array
    {
        return [
            MappingCollection::class,
            RouteSynchronizer::class,
            DatabaseMagicNumbersManager::class,
            ModelContainer::class,
            CalendarFactory::class,
            Calendar::class,
            DateShifter::class,
            DateCalculator::class,
            CompanyTelegram::class,
            OpenWeatherMap::class,
            WeatherForecast::class,
            SingleMiscManager::class,
            CompanyUsersComparator::class
        ];
    }


    /**
     * Все зарегистрированные классы должны быть перечислены в методе provides
     */
    public function register(): void
    {
        $this->app->singleton(MappingCollection::class, function (): YamlMappingCollection {
            return new YamlMappingCollection(
                ProviderHelper::createYamlCachingSettingsParser("file_mappings.yml")
            );
        });


        $this->app->singleton(RouteSynchronizer::class, function (Application $app): YamlRouteSynchronizer {
            return new YamlRouteSynchronizer(
                ProviderHelper::createYamlCachingSettingsParser("route_synchronization.yml"),
                $app->make(UrlGenerator::class)
            );
        });


        $this->app->singleton(DatabaseMagicNumbersManager::class, function (): YamlDatabaseMagicNumbersManager {
            return new YamlDatabaseMagicNumbersManager(
                ProviderHelper::createYamlCachingSettingsParser("database_magic_numbers.yml")
            );
        });


        $this->app->singleton(ModelContainer::class, function (): ModelContainer {
            return new ModelContainer();
        });


        $this->app->singleton(CalendarFactory::class, function (Application $app): CalendarCachingFactory {

            $factory = new YamlScheduleCalendarFactory(
                // Важно создать парсер, который не использует кэш, т.к.
                // кэширование реализовано на уровне CalendarCachingFactory
                ProviderHelper::createYamlSimpleSettingsParser("work_schedule.yml")
            );

            return new CalendarCachingFactory($factory, $app->make(CacheInterface::class));
        });


        $this->app->singleton(Calendar::class, function (Application $app): Calendar {
            return $app->make(CalendarFactory::class)->create();
        });


        $this->app->singleton(DateShifter::class);


        $this->app->singleton(DateCalculator::class);


        $this->app->singleton(CompanyTelegram::class, function (Application $app): CompanyTelegram {
            return new CompanyTelegram(
                $app->make(BotManager::class),
                config('services.company_telegram.main_chat.id')
            );
        });


        $this->app->singleton(OpenWeatherMap::class, function (Application $app): OpenWeatherMap {
            return new OpenWeatherMap(
                $app->make('config')->get('services.open_weather_map.api_key'),
                new GuzzleClient(),
                new GuzzleHttpFactory()
            );
        });


        $this->app->singleton(WeatherForecast::class, OpenWeatherMapAdapter::class);


        $this->app->singleton(SingleMiscManager::class, function (): YamlSingleMiscManager {
            return new YamlSingleMiscManager(
                ProviderHelper::createYamlCachingSettingsParser("miscs.yml")
            );
        });


        $this->app->singleton(CompanyUsersComparator::class, function (): CompanyUsersComparator {

            $baseComparator = new PriorityStringComparator([
                'Грищенко',
                'Громов',
                'Исаев'
            ]);
            return new CompanyUsersComparator($baseComparator);
        });
    }
}
