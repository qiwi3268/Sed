<?php

declare(strict_types=1);

namespace App\Repositories\Telegram\Polls;

use App\Repositories\Exceptions\EmptyResponseRepositoryException;
use App\Repositories\Exceptions\IneffectiveQueryRepositoryException;

use Illuminate\Database\PostgresConnection;
use App\Repositories\Vacations\VacationRepository;
use App\Lib\DatabaseMagicNumbers\MagicNumbersContainer;
use App\Repositories\Utils\Builder;
use App\Repositories\Utils\RepositorySupporter;
use DateTimeInterface;


final class PostgresAtWorkPollRepository implements AtWorkPollRepository
{

    public function __construct(
        private PostgresConnection $connection,
        private VacationRepository $vacationRepository,
        private MagicNumbersContainer $pollStatuses,
        private RepositorySupporter $supporter
    ) {}


    public function getActiveOptions(): array
    {
        return $this->connection->select("
            SELECT id, name
            FROM telegram_poll_option_at_works
            WHERE is_active = TRUE
            ORDER BY system_order
        ");
    }


    /**
     * @throws EmptyResponseRepositoryException
     */
    public function getOpenPollByDate(DateTimeInterface $date): object
    {
        $result = $this->connection->selectOne("
            SELECT
                telegram_poll_at_works.id as telegram_poll_at_work_id,
                telegram_polls.tg_message_id

            FROM telegram_poll_at_works

            JOIN telegram_polls
                ON telegram_poll_at_works.telegram_poll_id = telegram_polls.id

            WHERE
                telegram_poll_at_works.telegram_poll_status_id = {$this->pollStatuses->get('Открыт')}
                AND
                telegram_poll_at_works.created_at::date = ?

            LIMIT 1
        ", [$date->format('Y-m-d')]);

        return $result ?? throw new EmptyResponseRepositoryException(
            'telegram_poll_at_works',
            "telegram_poll_status_id = {$this->pollStatuses->get('Открыт')} AND created_at::date = {$date->format('Y-m-d')}"
            );
    }


    public function getByDate(DateTimeInterface $date): array
    {
        $polls = $this->connection->select("
            SELECT
                telegram_poll_at_works.id,
                telegram_poll_at_works.created_at,
                telegram_poll_at_works.finished_at,

                telegram_poll_statuses.id AS status_id,
                telegram_poll_statuses.name AS status_name,

                telegram_poll_option_at_works.id AS option_id,
                telegram_poll_option_at_works.name AS option_name,
                telegram_poll_created_option_at_works.id AS option_answer_id

            FROM telegram_poll_at_works

            JOIN telegram_poll_statuses
                ON telegram_poll_at_works.telegram_poll_status_id = telegram_poll_statuses.id
            JOIN telegram_poll_created_option_at_works
                ON telegram_poll_at_works.id = telegram_poll_created_option_at_works.telegram_poll_at_work_id
            JOIN telegram_poll_option_at_works
                ON telegram_poll_created_option_at_works.telegram_poll_option_at_work_id = telegram_poll_option_at_works.id

            WHERE telegram_poll_at_works.created_at::date = ?

            ORDER BY telegram_poll_created_option_at_works.option_index
        ", [$date->format('Y-m-d')]);

        if (empty($polls)) {
            return [];
        }

        $poll = Builder::create($polls)
            ->compileJoin('id', 'option_', 'options', false)
            ->stripPrefix('option_')
            ->singleToArray();

        $usersWithAnswer = $this->connection->select("
            SELECT
                users.id,
                users.last_name,
                users.first_name,
                users.middle_name,

                telegram_poll_answer_at_works.telegram_poll_created_option_at_work_id AS answer_id

            FROM telegram_accounts

            JOIN users
                ON telegram_accounts.user_id = users.id
            JOIN telegram_poll_answer_at_works
                ON telegram_accounts.id = telegram_poll_answer_at_works.telegram_account_id
            JOIN telegram_poll_created_option_at_works
                ON telegram_poll_answer_at_works.telegram_poll_created_option_at_work_id = telegram_poll_created_option_at_works.id

            WHERE telegram_poll_created_option_at_works.telegram_poll_at_work_id = ?
        ", [$poll['id']]);

        $users = Builder::create($usersWithAnswer);
        $ids = $users->pluck('id');

        $andWhere = $users->isEmpty()
            ? ''
            : 'AND id NOT IN (' . $this->supporter->createPlaceholders($ids) . ')';

        $usersWithoutAnswer = $this->connection->select("
            SELECT
                id,
                last_name,
                first_name,
                middle_name,
                null AS answer_id

            FROM users

            -- Пользователь создан раньше, чем опрос
            WHERE created_at < ? $andWhere
        ", [$date->format(DateTimeInterface::ISO8601), ...$ids]);

        $onVacation = $this->vacationRepository->getUsersOnVacationIdsByDate($date);

        $poll['users'] = $users
            ->addItems($usersWithoutAnswer)
            ->mutateItems(function (array &$user) use ($onVacation): void {
                $user['on_vacation'] = in_array($user['id'], $onVacation);
            })
            ->deleteKey('id')
            ->sortByKey('last_name')
            ->toArray();

        return $poll;
    }


    public function createAnswer(int $telegramAccountId, string $tgPollId, int $optionIndex): void
    {
        $this->connection->insert("
            INSERT INTO telegram_poll_answer_at_works
            (
                telegram_account_id,
                telegram_poll_created_option_at_work_id,
                created_at
            )
            VALUES
            (
                ?,
                (
                    SELECT telegram_poll_created_option_at_works.id

                    FROM telegram_polls

                    JOIN telegram_poll_at_works
                        ON telegram_polls.id = telegram_poll_at_works.telegram_poll_id
                    JOIN telegram_poll_created_option_at_works
                        ON telegram_poll_at_works.id = telegram_poll_created_option_at_works.telegram_poll_at_work_id

                    WHERE
                        telegram_polls.tg_poll_id = ?
                        AND
                        telegram_poll_created_option_at_works.option_index = ?
                ),
                now()
            )
        ", [$telegramAccountId, $tgPollId, $optionIndex]);
    }


    /**
     * @throws IneffectiveQueryRepositoryException
     */
    public function deleteAnswers(int $telegramAccountId, string $tgPollId): void
    {
        $count = $this->connection->delete("
            DELETE
            FROM telegram_poll_answer_at_works
            WHERE
                telegram_poll_answer_at_works.telegram_account_id = ?
                AND
                telegram_poll_answer_at_works.telegram_poll_created_option_at_work_id IN (
                    SELECT telegram_poll_created_option_at_works.id

                    FROM telegram_polls

                    JOIN telegram_poll_at_works
                        ON telegram_polls.id = telegram_poll_at_works.telegram_poll_id
                    JOIN telegram_poll_created_option_at_works
                        ON telegram_poll_at_works.id = telegram_poll_created_option_at_works.telegram_poll_at_work_id

                    WHERE telegram_polls.tg_poll_id = ?
                );
        ", [$telegramAccountId, $tgPollId]);

        if ($count == 0) {
            IneffectiveQueryRepositoryException::ineffectiveDeletion();
        }
    }


    /**
     * @throws IneffectiveQueryRepositoryException
     */
    public function stopPoll(int $telegramPollAtWorkId): void
    {
        $count = $this->connection->update("
            UPDATE telegram_poll_at_works
            SET
                telegram_poll_status_id = {$this->pollStatuses->get('Закрыт')},
                finished_at = now()
            WHERE
                id = ?
                AND
                telegram_poll_status_id = {$this->pollStatuses->get('Открыт')}
        ", [$telegramPollAtWorkId]);

        if ($count == 0) {
            IneffectiveQueryRepositoryException::ineffectiveUpdate();
        }
    }
}
