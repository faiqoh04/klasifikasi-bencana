<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\PredictionHistoryController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/classification', [ClassificationController::class, 'index'])->name('classification.index');
Route::post('/classification/predict', [ClassificationController::class, 'predict'])->name('classification.predict');
Route::post('/classification/import', [ClassificationController::class, 'import'])->name('classification.import');

Route::get('/history', [PredictionHistoryController::class, 'index'])->name('history.index');
Route::get('/history/{id}', [PredictionHistoryController::class, 'show'])->name('history.show');