<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\{
    CriterionController,
    AhpController,
    AlternativeController,
    ScoreController,
    DecisionController
};

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Criteria Management
    Route::resource('criteria', CriterionController::class);
    Route::get('criteria-deleted', [CriterionController::class, 'deleted'])->name('criteria.deleted');
    Route::post('criteria/{id}/restore', [CriterionController::class, 'restore'])->name('criteria.restore');
    Route::delete('criteria/{id}/force-delete', [CriterionController::class, 'forceDelete'])->name('criteria.force-delete');

    // AHP Session
    Route::resource('ahp', AhpController::class)->except(['edit', 'update']);
    Route::post('ahp/{ahp}/activate', [AhpController::class, 'activate'])->name('ahp.activate');
    Route::post('ahp/{ahp}/deactivate', [AhpController::class, 'deactivate'])->name('ahp.deactivate');

    // Alternatives (CS Data)
    Route::resource('alternatives', AlternativeController::class);
    Route::post('alternatives/bulk-status', [AlternativeController::class, 'bulkUpdateStatus'])->name('alternatives.bulk-status');

    // Scores (Penilaian CS)
    Route::get('scores', [ScoreController::class, 'index'])->name('scores.index');
    Route::get('scores/edit', [ScoreController::class, 'edit'])->name('scores.edit');
    Route::put('scores', [ScoreController::class, 'update'])->name('scores.update');

    // Decisions (TOPSIS)
    Route::resource('decisions', DecisionController::class)->except(['edit', 'update']);
});

// Redirect root to dashboard if authenticated, otherwise to login
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});
