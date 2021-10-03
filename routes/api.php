<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TestController;

use App\Http\Controllers\Api\Auth\AuthController;

use App\Http\Controllers\Api\Miscs\SingleMiscController;

use App\Http\Controllers\Api\Files\FileRuleController;
use App\Http\Controllers\Api\Files\FileUploadController;
use App\Http\Controllers\Api\Files\FileCheckController;
use App\Http\Controllers\Api\Files\FileHashController;
use App\Http\Controllers\Api\Files\SignatureValidation\ExternalSignatureValidationController;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Birthdays\BirthdayController;

use App\Http\Controllers\Api\SignatureSessions\SignatureSessionController;
use App\Http\Controllers\Api\SignatureSessions\SignatureSessionGuardController;
use App\Http\Controllers\Api\SignatureSessions\Navigation\SignatureSessionNavigationCounterController;
use App\Http\Controllers\Api\SignatureSessions\Navigation\SignatureSessionNavigationItemController;

use App\Http\Controllers\Api\Vacations\VacationController;
use App\Http\Controllers\Api\Vacations\VacationGuardController;

use App\Http\Controllers\Api\Conferences\ConferenceController;
use App\Http\Controllers\Api\Conferences\Navigation\ConferenceNavigationAllController;
use App\Http\Controllers\Api\Conferences\Navigation\ConferenceNavigationCounterController;
use App\Http\Controllers\Api\Conferences\Navigation\ConferenceNavigationItemController;
use App\Http\Controllers\Api\Conferences\ConferenceGuardController;

use App\Lib\Permissions\Permissions;

use App\Http\Controllers\Api\Telegram\Polls\AtWorkPollController as TelegramAtWorkPollController;



Route::get('test', [TestController::class, 'test']);


Route::prefix('auth')->group(function () {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::middleware('auth')->group(function() {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});


Route::middleware('auth')->group(function() {

    Route::prefix('miscs')->group(function () {
        Route::get('single', [SingleMiscController::class, 'show']);
    });

    Route::prefix('files')->group(function () {
        Route::get('rule', [FileRuleController::class, 'rule']);
        Route::post('upload', [FileUploadController::class, 'upload']);
        Route::get('check', [FileCheckController::class, 'check']);

        Route::prefix('csp')->group(function () {
            Route::get('hash', [FileHashController::class, 'hash']);
            Route::post('externalSignatureValidation', [ExternalSignatureValidationController::class, 'validateSignature']);
        });
    });


    Route::get('users', [UserController::class, 'index']);

    Route::get('birthdays', [BirthdayController::class, 'index']);


    Route::prefix('signatureSessions')->group(function () {

        Route::post('create', [SignatureSessionController::class, 'create']);
        Route::post('delete', [SignatureSessionController::class, 'delete']);
        Route::get('show', [SignatureSessionController::class, 'show']);
        Route::get('signing', [SignatureSessionController::class, 'showSigning']);
        Route::post('sign', [SignatureSessionController::class, 'createSign']);

        Route::prefix('navigation')->group(function () {

            Route::prefix('counters')->group(function () {
                Route::get('waitingAction', [SignatureSessionNavigationCounterController::class, 'waitingAction']);
                Route::get('inWork', [SignatureSessionNavigationCounterController::class, 'inWork']);
                Route::get('finished', [SignatureSessionNavigationCounterController::class, 'finished']);
            });

            Route::prefix('items')->group(function () {
                Route::get('waitingAction', [SignatureSessionNavigationItemController::class, 'waitingAction']);
                Route::get('inWork', [SignatureSessionNavigationItemController::class, 'inWork']);
                Route::get('finished', [SignatureSessionNavigationItemController::class, 'finished']);
            });
        });

        Route::prefix('guards')->group(function () {
            Route::get('canUserSign', [SignatureSessionGuardController::class, 'canUserSign']);
            Route::get('canUserDelete', [SignatureSessionGuardController::class, 'canUserDelete']);
        });
    });


    Route::prefix('vacations')->group(function () {

        Route::middleware('can:' . Permissions::LIST['manage_vacations'])->group(function () {

            Route::post('create', [VacationController::class, 'create']);
            Route::post('update', [VacationController::class, 'update']);
            Route::post('delete', [VacationController::class, 'delete']);

            Route::prefix('show')->group(function () {
                Route::get('next', [VacationController::class, 'showNext']);
                Route::get('past', [VacationController::class, 'showPast']);
            });
        });

        Route::prefix('show')->group(function () {
            Route::get('forNext30Days', [VacationController::class, 'showForNext30Days']);
            Route::get('forYearAndMonth', [VacationController::class, 'showForYearAndMonth']);
            Route::get('forCurrentDate', [VacationController::class, 'showForCurrentDate']);
        });

        Route::get('percentForCurrentDate', [VacationController::class, 'percentForCurrentDate']);

        Route::prefix('guards')->group(function () {
            Route::get('canUserManage', [VacationGuardController::class, 'canUserManage']);
        });
    });


    Route::prefix('conferences')->group(function () {

        Route::post('create', [ConferenceController::class, 'create'])
            ->middleware('can:create_conferences');

        Route::post('update', [ConferenceController::class, 'update']);

        Route::post('delete', [ConferenceController::class, 'delete']);

        Route::get('show', [ConferenceController::class, 'show']);

        Route::get('showForUpdate', [ConferenceController::class, 'showForUpdate']);

        Route::prefix('navigation')->group(function () {

            Route::prefix('counters')->group(function () {

                Route::prefix('my')->group(function () {
                    Route::get('todays', [ConferenceNavigationCounterController::class, 'myTodays']);
                    Route::get('planned', [ConferenceNavigationCounterController::class, 'myPlanned']);
                });
            });

            Route::prefix('items')->group(function () {

                Route::prefix('my')->group(function () {
                    Route::get('todays', [ConferenceNavigationItemController::class, 'myTodays']);
                    Route::get('planned', [ConferenceNavigationItemController::class, 'myPlanned']);
                });

                Route::get('all/forDate', [ConferenceNavigationItemController::class, 'allForDate']);
            });

            Route::get('all/datesWithConferencesForYear', [ConferenceNavigationAllController::class, 'datesWithConferencesForYear']);
        });

        Route::prefix('guards')->group(function () {
            Route::get('canUserCreate', [ConferenceGuardController::class, 'canUserCreate']);
            Route::get('canUserUpdate', [ConferenceGuardController::class, 'canUserUpdate']);
            Route::get('canUserDelete', [ConferenceGuardController::class, 'canUserDelete']);
        });
    });


    Route::prefix('polls')->group(function () {

        Route::prefix('atWork')->group(function () {
            Route::get('showForDate', [TelegramAtWorkPollController::class, 'showForDate']);
        });
    });
});
