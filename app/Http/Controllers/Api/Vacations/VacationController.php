<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vacations;

use Throwable;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Vacations\CreateRequest;
use App\Http\Requests\Api\Vacations\UpdateRequest;
use App\Http\Requests\Api\Vacations\DeleteRequest;
use App\Http\Requests\Api\Vacations\ShowForYearAndMonthRequest;
use App\Models\User;
use App\Models\Vacations\Vacation;
use App\Lib\DateShifter\DateShifter;
use App\Services\RequestValidation\ModelContainer;
use App\Repositories\Vacations\VacationRepository;


final class VacationController extends Controller
{
    public function __construct(private DateShifter $dateShifter)
    {}


    /**
     * @throws Throwable
     */
    public function create(CreateRequest $request): JsonResponse
    {
        $replacements = array_map(fn (int $id) => ['replacement_user_id' => $id], $request->replacementIds);

        DB::transaction(function () use ($request, $replacements): void {

            $vacation = Vacation::create([
                'author_id' => $request->user()->id,
                'user_id'   => $request->userId,
                'start_at'  => $request->startAt,
                // - 1, т.к. start_at уже считается первым днём отпуска
                'finish_at' => $this->dateShifter->shiftOnCalendarDaysWithHolidays($request->startAt, $request->duration - 1),
                'duration'  => $request->duration,
            ]);

            $vacation->replacements()->createMany($replacements);
        });

        return success_response();
    }


    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, ModelContainer $modelContainer): JsonResponse
    {
        $vacation = $modelContainer->get(Vacation::class, (string) $request->vacationId);

        $replacements = array_map(fn (int $id) => ['replacement_user_id' => $id], $request->replacementIds);

        DB::transaction(function () use ($request, $vacation, $replacements): void {

            $vacation->update([
                'user_id'   => $request->userId,
                'start_at'  => $request->startAt,
                // - 1, т.к. start_at уже считается первым днём отпуска
                'finish_at' => $this->dateShifter->shiftOnCalendarDaysWithHolidays($request->startAt, $request->duration - 1),
                'duration'  => $request->duration,
            ]);

            $vacation->replacements()->delete();
            $vacation->replacements()->createMany($replacements);
            $vacation->touch();
        });

        return success_response();
    }


    public function delete(DeleteRequest $request, ModelContainer $modelContainer): JsonResponse
    {
        $modelContainer->get(Vacation::class, (string) $request->vacationId)->delete();
        return success_response();
    }


    public function showForNext30Days(VacationRepository $repository): JsonResponse
    {
        return success_response(data: $repository->getForNext30Days());
    }


    public function showForYearAndMonth(ShowForYearAndMonthRequest $request, VacationRepository $repository): JsonResponse
    {
        return success_response(data: $repository->getByYearAndMonth(
            $request->year,
            $request->month
        ));
    }


    public function percentForCurrentDate(VacationRepository $repository): JsonResponse
    {
        $onVacation = $repository->getUsersOnVacationCountByDate(now());

        return success_response(data: ['percent' => (int) ceil($onVacation * 100 / User::count())]);
    }


    public function showForCurrentDate(VacationRepository $repository): JsonResponse
    {
        return success_response(data: $repository->getUsersOnVacationByDate(now()));
    }


    public function showNext(VacationRepository $repository): JsonResponse
    {
        return success_response(data: $repository->getNext());
    }


    public function showPast(VacationRepository $repository): JsonResponse
    {
        return success_response(data: $repository->getPast());
    }
}
