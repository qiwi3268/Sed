<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\SignatureSessions\SignatureSession;
use App\Repositories\SignatureSessions\SignatureSessionRepository;


final class SignatureSessionPolicy
{
    use HandlesAuthorization;


    public function __construct(private SignatureSessionRepository $signatureSessionRepository)
    {}


    /**
     * @param User $user модель со всеми полями
     * @param SignatureSession $signatureSession модель со всеми полями
     */
    public function createSign(User $user, SignatureSession $signatureSession): Response
    {
        $can = $this->signatureSessionRepository->canUserSign(
            $user->uuid,
            $signatureSession->uuid,
        );

        return $can
            ? $this->allow()
            : $this->deny('Пользователь не имеет доступа к сессии подписания');
    }


    /**
     * @param User $user модель со всеми полями
     * @param SignatureSession $signatureSession модель со всеми полями
     */
    public function delete(User $user, SignatureSession $signatureSession): Response
    {
        $can = $this->signatureSessionRepository->canUserDelete(
            $user->uuid,
            $signatureSession->uuid,
        );

        return $can
            ? $this->allow()
            : $this->deny('Пользователь не может удалить сессию подписания');
    }
}
