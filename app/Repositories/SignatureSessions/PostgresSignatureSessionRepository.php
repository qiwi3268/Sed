<?php

declare(strict_types=1);

namespace App\Repositories\SignatureSessions;

use JsonException;
use App\Repositories\Exceptions\EmptyResponseRepositoryException;

use Illuminate\Database\PostgresConnection;
use App\Lib\DatabaseMagicNumbers\MagicNumbersContainer;
use App\Lib\Csp\SignatureValidation\Entities\ValidationResultFactory;
use App\Services\Files\ResponseSerialization\ValidationResultSerializer;
use stdClass;


final class PostgresSignatureSessionRepository implements SignatureSessionRepository
{
    public function __construct(
        private PostgresConnection $connection,
        private MagicNumbersContainer $sessionStatuses,
        private MagicNumbersContainer $signerStatuses
    ) {}


    /**
     * @throws JsonException
     * @throws EmptyResponseRepositoryException
     */
    public function get(string $signatureSessionUuid): object
    {
        $session = $this->connection->selectOne("
            SELECT
                signature_sessions.id,
                signature_sessions.uuid,
                signature_sessions.title,
                signature_sessions.created_at,

                signature_session_statuses.id AS status_id,
                signature_session_statuses.name AS status_name,

                users.last_name AS author_last_name,
                users.first_name AS author_first_name,
                users.middle_name AS author_middle_name,

                signature_session_files.original_name AS file_original_name,
                signature_session_files.size AS file_size,
                signature_session_files.star_path AS file_star_path,

                zip_archive_files.star_path AS zip_archive_star_path

            FROM signature_sessions

            JOIN signature_session_statuses
                ON signature_sessions.signature_session_status_id = signature_session_statuses.id
            JOIN users
                ON signature_sessions.author_id = users.id
            JOIN files AS signature_session_files
                ON signature_sessions.file_id = signature_session_files.id
            LEFT JOIN signature_session_zip_archives
                ON signature_sessions.id = signature_session_zip_archives.signature_session_id
            LEFT JOIN files AS zip_archive_files
                ON signature_session_zip_archives.file_id = zip_archive_files.id

            WHERE signature_sessions.uuid = ?
        ", [$signatureSessionUuid]);

        $session ?? throw new EmptyResponseRepositoryException('signature_sessions', "uuid = $signatureSessionUuid");

        $signers = $this->connection->select("
            SELECT
                users.last_name,
                users.first_name,
                users.middle_name,

                signature_session_signer_assignments.signed_at,
                signature_session_signer_statuses.id AS status_id,
                signature_session_signer_statuses.name AS status_name,

                file_external_signatures.validation_result

            FROM signature_session_signer_assignments

            JOIN users
                ON signature_session_signer_assignments.signer_id = users.id
            JOIN signature_session_signer_statuses
                ON signature_session_signer_assignments.signature_session_signer_status_id = signature_session_signer_statuses.id
            LEFT JOIN signature_session_signed_files
                ON signature_session_signer_assignments.id = signature_session_signed_files.signature_session_signer_assignment_id
            LEFT JOIN file_external_signatures
                ON signature_session_signed_files.file_external_signature_id = file_external_signatures.id

            WHERE signature_session_signer_assignments.signature_session_id = ?

            ORDER BY signature_session_signer_assignments.signed_at
        ", [$session->id]);

        $session->signers = (object) arr_split($signers, [
            'signed'   => fn (stdClass $s): bool => $s->status_id == $this->signerStatuses->get('Подписано'),
            'unsigned' => fn (stdClass $s): bool => $s->status_id != $this->signerStatuses->get('Подписано'),
        ]);

        foreach ($session->signers->signed as $signer) {
            $signer->validation_result = new ValidationResultSerializer(
                ValidationResultFactory::createFromJson($signer->validation_result)
            );
        }

        return $session;
    }


    /**
     * @throws EmptyResponseRepositoryException
     */
    public function getForSigning(string $signatureSessionUuid): object
    {
        $session = $this->connection->selectOne("
            SELECT
                signature_sessions.uuid,
                signature_sessions.title,
                signature_sessions.created_at,

                signature_session_statuses.id AS status_id,
                signature_session_statuses.name AS status_name,

                users.last_name AS author_last_name,
                users.first_name AS author_first_name,
                users.middle_name AS author_middle_name,

                files.original_name AS file_original_name,
                files.size AS file_size,
                files.star_path AS file_star_path

            FROM signature_sessions

            JOIN signature_session_statuses
                ON signature_sessions.signature_session_status_id = signature_session_statuses.id
            JOIN users
                ON signature_sessions.author_id = users.id
            JOIN files
                ON signature_sessions.file_id = files.id

            WHERE signature_sessions.uuid = ?
        ", [$signatureSessionUuid]);

        return $session ?? throw new EmptyResponseRepositoryException('signature_sessions', "uuid = $signatureSessionUuid");
    }


    public function canUserSign(string $userUuid, string $signatureSessionUuid): bool
    {
        $result = $this->connection->selectOne("
            SELECT EXISTS(
                SELECT *

                FROM signature_session_signer_assignments

                JOIN signature_sessions
                    ON signature_session_signer_assignments.signature_session_id = signature_sessions.id
                JOIN users
                    ON signature_session_signer_assignments.signer_id = users.id

                WHERE
                    signature_session_signer_status_id = {$this->signerStatuses->get('Ожидает подписания')}
                    AND
                    signature_sessions.uuid = ?
                    AND
                    users.uuid = ?
            );
        ", [$signatureSessionUuid, $userUuid]);

        return $result->exists;
    }


    public function canUserDelete(string $userUuid, string $signatureSessionUuid): bool
    {
        $result = $this->connection->selectOne("
            SELECT EXISTS(
                SELECT *

                FROM signature_sessions

                JOIN users
                    ON signature_sessions.author_id = users.id

                WHERE
                    signature_session_status_id != {$this->sessionStatuses->get('Завершена')}
                    AND
                    signature_sessions.uuid = ?
                    AND
                    users.uuid = ?
            );
        ", [$signatureSessionUuid, $userUuid]);

        return $result->exists;
    }


    public function isAllSigned(string $signatureSessionUuid): bool
    {
        $result = $this->connection->selectOne("
            SELECT EXISTS(
                SELECT *

                FROM signature_session_signer_assignments

                JOIN signature_sessions
                    ON signature_session_signer_assignments.signature_session_id = signature_sessions.id

                WHERE
                    signature_session_signer_status_id != {$this->signerStatuses->get('Подписано')}
                    AND
                    signature_sessions.uuid = ?
            );
        ", [$signatureSessionUuid]);

        return !$result->exists;
    }


    /**
     * @throws EmptyResponseRepositoryException
     */
    public function getFiles(string $signatureSessionUuid): array
    {
        $result = $this->connection->select("
            SELECT
                files.star_path AS file_star_path,
                files.original_name AS file_original_name,

                file_external_signatures.star_path AS external_signature_star_path,
                file_external_signatures.created_name AS external_signature_name

            FROM signature_sessions

            JOIN files
                ON signature_sessions.file_id = files.id
            JOIN signature_session_signer_assignments
                ON signature_sessions.id = signature_session_signer_assignments.signature_session_id
            LEFT JOIN signature_session_signed_files
                ON signature_session_signer_assignments.id = signature_session_signed_files.signature_session_signer_assignment_id
            LEFT JOIN file_external_signatures
                ON signature_session_signed_files.file_external_signature_id = file_external_signatures.id

            WHERE signature_sessions.uuid = ?

        ", [$signatureSessionUuid]);

        if (empty($result)) {
            throw new EmptyResponseRepositoryException('signature_sessions');
        }

        $files = [
            'file' => [
                'starPath' => $result[0]->file_star_path,
                'name'     => $result[0]->file_original_name
            ],
            'externalSignatures' => []
        ];

        foreach ($result as $object) {

            $starPath = $object->external_signature_star_path;
            $name     = $object->external_signature_name;

            if (!is_null($starPath) && !is_null($name)) {

                $files['externalSignatures'][] = [
                    'starPath' => $starPath,
                    'name'     => $name
                ];
            }
        }
        return $files;
    }
}
