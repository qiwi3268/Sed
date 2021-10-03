<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\PostgresConnection;

use App\Repositories\Files\Services\DatabaseManagementRepository;
use App\Repositories\Files\Services\PostgresDatabaseManagementRepository;
use App\Repositories\Files\Services\FilesystemDeletionRepository;
use App\Repositories\Files\Services\PostgresFilesystemDeletionRepository;
use App\Repositories\SignatureSessions\Navigation\SignatureSessionNavigationRepository;
use App\Repositories\SignatureSessions\Navigation\PostgresSignatureSessionNavigationRepository;
use App\Repositories\SignatureSessions\SignatureSessionRepository;
use App\Repositories\SignatureSessions\PostgresSignatureSessionRepository;
use App\Repositories\Telegram\Polls\AtWorkPollRepository;
use App\Repositories\Telegram\Polls\PostgresAtWorkPollRepository;
use App\Repositories\Vacations\VacationRepository;
use App\Repositories\Vacations\PostgresVacationRepository;
use App\Repositories\Conferences\Navigation\ConferenceNavigationRepository;
use App\Repositories\Conferences\Navigation\PostgresConferenceNavigationRepository;
use App\Repositories\Conferences\ConferenceRepository;
use App\Repositories\Conferences\PostgresConferenceRepository;
use App\Repositories\Birthdays\BirthdayRepository;
use App\Repositories\Birthdays\PostgresBirthdayRepository;
use App\Lib\DatabaseMagicNumbers\DatabaseMagicNumbersManager;
use App\Lib\DatabaseMagicNumbers\MagicNumbersContainer;
use App\Repositories\Utils\RepositorySupporter;
use App\Lib\DateShifter\DateCalculator;


final class RepositoryRegisterServiceProvider extends ServiceProvider implements DeferrableProvider
{

    public function provides(): array
    {
        return [
            SignatureSessionNavigationRepository::class,
            SignatureSessionRepository::class,
            DatabaseManagementRepository::class,
            FilesystemDeletionRepository::class,
            AtWorkPollRepository::class,
            VacationRepository::class,
            ConferenceNavigationRepository::class,
            ConferenceRepository::class,
            BirthdayRepository::class
        ];
    }


    /**
     * Все зарегистрированные репозитории должны быть перечислены в методе provides
     */
    public function register(): void
    {
        $this->app->singleton(SignatureSessionNavigationRepository::class, function (Application $app): PostgresSignatureSessionNavigationRepository {
            return new PostgresSignatureSessionNavigationRepository(
                $this->getPostgresConnection(),
                $this->getMagicNumbersContainer('signature_session_statuses'),
                $this->getMagicNumbersContainer('signature_session_signer_statuses'),
                $app->make(RepositorySupporter::class)
            );
        });


        $this->app->singleton(SignatureSessionRepository::class, function (): PostgresSignatureSessionRepository {
            return new PostgresSignatureSessionRepository(
                $this->getPostgresConnection(),
                $this->getMagicNumbersContainer('signature_session_statuses'),
                $this->getMagicNumbersContainer('signature_session_signer_statuses')
            );
        });


        $this->app->singleton(DatabaseManagementRepository::class, function (): PostgresDatabaseManagementRepository {
            return new PostgresDatabaseManagementRepository(
                23,
                $this->getPostgresConnection()
            );
        });


        $this->app->singleton(FilesystemDeletionRepository::class, function (Application $app): PostgresFilesystemDeletionRepository {
            return new PostgresFilesystemDeletionRepository(
                $this->getPostgresConnection(),
                $app->make(RepositorySupporter::class)
            );
        });


        $this->app->singleton(AtWorkPollRepository::class, function (Application $app): PostgresAtWorkPollRepository {
            return new PostgresAtWorkPollRepository(
                $this->getPostgresConnection(),
                $app->make(VacationRepository::class),
                $this->getMagicNumbersContainer('telegram_poll_statuses'),
                $app->make(RepositorySupporter::class)
            );
        });


        $this->app->singleton(VacationRepository::class, function (Application $app): PostgresVacationRepository {
            return new PostgresVacationRepository(
                $this->getPostgresConnection(),
                $app->make(DateCalculator::class),
                $app->make(RepositorySupporter::class)
            );
        });


        $this->app->singleton(ConferenceNavigationRepository::class, function (Application $app): PostgresConferenceNavigationRepository {
            return new PostgresConferenceNavigationRepository(
                $this->getPostgresConnection(),
                $app->make(RepositorySupporter::class)
            );
        });


        $this->app->singleton(ConferenceRepository::class, function (Application $app): PostgresConferenceRepository {
            return new PostgresConferenceRepository($this->getPostgresConnection());
        });


        $this->app->singleton(BirthdayRepository::class, function (Application $app): PostgresBirthdayRepository {
            return new PostgresBirthdayRepository(
                $this->getPostgresConnection(),
                $app->make(RepositorySupporter::class)
            );
        });
    }


    /**
     * @throws BindingResolutionException
     */
    private function getPostgresConnection(): PostgresConnection
    {
        /** @var PostgresConnection $connection */
        static $connection;

        return $connection ??= $this->app
            ->make(ConnectionResolverInterface::class)
            ->connection('pgsql');
    }


    /**
     * @throws BindingResolutionException
     */
    private function getMagicNumbersContainer(string $name): MagicNumbersContainer
    {
        /** @var DatabaseMagicNumbersManager $manager */
        $manager = $this->app->make(DatabaseMagicNumbersManager::class);

        return $manager->getContainer($name);
    }
}
