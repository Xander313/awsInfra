<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Risk\RiskController;
use App\Http\Controllers\Risk\DpiaController;

/*
|--------------------------------------------------------------------------
| Fase 9 - RISK (Riesgos + DPIA)
|--------------------------------------------------------------------------
| NOTA: Este archivo debe ser incluido desde routes/web.php:
|   require __DIR__.'/risk.php';
*/

Route::prefix('risk')->group(function () {

    // ==========================
    // UI (Blade)
    // ==========================
    Route::view('ui', 'risk.index')->name('risk.ui');
    Route::view('ui/risks', 'risk.risks.index')->name('risk.ui.risks');
    Route::view('ui/dpias', 'risk.dpias.index')->name('risk.ui.dpias');

    // ==========================
    // META (dropdowns / catálogos)
    // ==========================
    Route::get('meta/orgs', [RiskController::class, 'orgs'])->name('risk.meta.orgs');
    Route::get('meta/processing-activities', [DpiaController::class, 'processingActivities'])->name('risk.meta.processing_activities');

    // ==========================
    // API - CRUD Risk (risk.risk)
    // ==========================
    Route::resource('risks', RiskController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    // ==========================
    // API - CRUD DPIA (risk.dpia)
    // ==========================
    Route::resource('dpias', DpiaController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    // ==========================
    // API - N:M DPIA <-> Risk (risk.dpia_risk)
    // ==========================
    Route::post('dpias/{dpia}/risks', [DpiaController::class, 'attachRisk'])->name('dpias.risks.attach');
    Route::put('dpias/{dpia}/risks/{risk}', [DpiaController::class, 'updateRiskRationale'])->name('dpias.risks.update');
    Route::delete('dpias/{dpia}/risks/{risk}', [DpiaController::class, 'detachRisk'])->name('dpias.risks.detach');

    // ==========================
    // API - Resumen por actividad
    // ==========================
    Route::get('processing-activities/{pa_id}/dpia-summary', [DpiaController::class, 'summaryByActivity'])
        ->name('dpias.summary.activity');
});
