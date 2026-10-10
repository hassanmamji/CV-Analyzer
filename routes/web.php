<?php

use App\Http\Controllers\CvAnalysisController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CvAnalysisController::class, 'create'])->name('cv.create');
Route::post('/analyze', [CvAnalysisController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('cv.store');
Route::get('/result/{analysis}', [CvAnalysisController::class, 'show'])->name('cv.show');