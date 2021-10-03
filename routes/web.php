<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TestController;
use App\Http\Controllers\Files\FileController;
use App\Http\Controllers\Files\SignatureSessions\FileSignatureSessionController;


Route::get('test', [TestController::class, 'test']);

Route::prefix('files')->group(function () {
    Route::get('download', [FileController::class, 'download']);
    Route::get('signatureSessions/downloadGeneratedZip', [FileSignatureSessionController::class, 'downloadGeneratedZip']);
});


// SPA
Route::fallback(function () {
    return view('spa');
});
