<?php

declare(strict_types=1);

namespace App\Models\Conferences;

use DomainException;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;

use App\Models\AppModel;
use App\Models\Miscs\Conferences\MiscConferenceForm;
use App\Models\Miscs\Conferences\MiscConferenceLocation;
use App\Models\User;


/**
 * @property-read MiscConferenceForm $miscConferenceFormName
 * @property-read MiscConferenceLocation $miscConferenceLocationName
 * @property-read ?User $vksConnectionResponsible
 * @property-read Collection|User[] $members
 * @mixin IdeHelperConference
 */
final class Conference extends AppModel
{
    protected $casts = [
        'start_at' => 'immutable_datetime',
        'is_notification_before_start_sent' => 'bool'
    ];

    protected $fillable = [
        'uuid',
        'author_id',
        'topic',
        'start_at',
        'misc_conference_form_id',
        'vks_href',
        'vks_connection_responsible_id',
        'misc_conference_location_id',
        'outer_members',
        'comment',
        'is_notification_before_start_sent'
    ];


    /**
     * Обновляет флаг того, что уведомление перед началом совещания было отправлено
     *
     * @throws DomainException
     */
    public function notificationBeforeStartSent(): self
    {
        if ($this->getRequiredAttribute('is_notification_before_start_sent') === true) {
            throw new DomainException('Уведомление перед началом совещания уже было отправлено');
        }
        $this->is_notification_before_start_sent = true;
        $this->update();
        return $this;
    }


    public function miscConferenceFormName(): BelongsTo
    {
        return $this->belongsTo(
            MiscConferenceForm::class, 'misc_conference_form_id'
        )->select(['name']);
    }


    public function miscConferenceLocationName(): BelongsTo
    {
        return $this->belongsTo(
            MiscConferenceLocation::class, 'misc_conference_location_id'
        )->select(['name']);
    }


    public function vksConnectionResponsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vks_connection_responsible_id');
    }


    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
