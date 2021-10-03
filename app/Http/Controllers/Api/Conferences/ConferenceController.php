<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Conferences;

use Throwable;
use Illuminate\Auth\Access\AuthorizationException;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Http\Requests\Api\Conferences\CreateRequest;
use App\Http\Requests\Api\Conferences\UpdateRequest;
use App\Http\Requests\Api\Conferences\DeleteRequest;
use App\Http\Requests\Api\Conferences\ShowRequest;
use App\Http\Requests\Api\Conferences\ShowForUpdateRequest;
use App\Http\Controllers\Controller;
use App\Models\Conferences\Conference;
use App\Repositories\Conferences\ConferenceRepository;
use App\Services\RequestValidation\ModelContainer;
use App\Jobs\Conferences\ProcessConferencesBeforeStarting;
use App\Events\Conferences\ConferenceCreated;
use App\Events\Conferences\ConferenceUpdated;
use App\Events\Conferences\ConferenceDeleted;


final class ConferenceController extends Controller
{

    /**
     * @throws Throwable
     */
    public function create(CreateRequest $request): JsonResponse
    {
        $conference = DB::transaction(function () use ($request): Conference {

            $conference = Conference::create([
                'uuid'                              => Str::uuid()->toString(),
                'author_id'                         => $request->user()->id,
                'topic'                             => $request->topic,
                'start_at'                          => $request->startAt,
                'misc_conference_form_id'           => $request->conferenceForm,
                'vks_href'                          => $request->vksHref,
                'vks_connection_responsible_id'     => $request->vksConnectionResponsibleId,
                'misc_conference_location_id'       => $request->conferenceLocation,
                'outer_members'                     => $request->outerMembers,
                'comment'                           => $request->comment,
                // Уведомление перед началом совещания считается уже отправленным, если
                // осталось меньше времени, чем в ProcessConferencesBeforeStarting
                'is_notification_before_start_sent' => now()->diffInMinutes($request->startAt) <= ProcessConferencesBeforeStarting::MINUTES_BEFORE_START
            ]);

            $conference->members()->attach($request->memberIds);

            return $conference;
        });

        ConferenceCreated::dispatch($conference);

        return success_response();
    }


    /**
     * @throws Throwable
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, ModelContainer $modelContainer): JsonResponse
    {
        $conference = $modelContainer->get(Conference::class, $request->conferenceUuid);

        $this->authorize('update', $conference);

        DB::transaction(function () use ($request, $conference): void {

            $conference->update([
                'topic'                             => $request->topic,
                'start_at'                          => $request->startAt,
                'misc_conference_form_id'           => $request->conferenceForm,
                'vks_href'                          => $request->vksHref,
                'vks_connection_responsible_id'     => $request->vksConnectionResponsibleId,
                'misc_conference_location_id'       => $request->conferenceLocation,
                'outer_members'                     => $request->outerMembers,
                'comment'                           => $request->comment,
                // Уведомление перед началом совещания считается уже отправленным, если
                // осталось меньше времени, чем в ProcessConferencesBeforeStarting
                'is_notification_before_start_sent' => now()->diffInMinutes($request->startAt) <= ProcessConferencesBeforeStarting::MINUTES_BEFORE_START
            ]);

            $conference->members()->sync($request->memberIds);
        });

        ConferenceUpdated::dispatch($conference);

        return success_response();
    }


    /**
     * @throws AuthorizationException
     */
    public function delete(DeleteRequest $request, ModelContainer $modelContainer): JsonResponse
    {
        $conference = $modelContainer->get(Conference::class, $request->conferenceUuid);

        $this->authorize('delete', $conference);

        $conference->delete();

        ConferenceDeleted::dispatch($conference->topic,  $conference->start_at);

        return success_response();
    }


    public function show(ShowRequest $request, ConferenceRepository $repository): JsonResponse
    {
        return success_response(data: $repository->get($request->conferenceUuid));
    }


    /**
     * @throws AuthorizationException
     */
    public function showForUpdate(ShowForUpdateRequest $request, ConferenceRepository $repository, ModelContainer $modelContainer): JsonResponse
    {
        $conference = $modelContainer->get(Conference::class, $request->conferenceUuid);

        $this->authorize('update', $conference);

        return success_response(data: $repository->getForUpdate($request->conferenceUuid));
    }
}
