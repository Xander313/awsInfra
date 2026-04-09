<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Risk\SanctionCoefficientController;
use App\Http\Controllers\Risk\SanctionSimulationController;
use App\Http\Controllers\Risk\SanctionWizardController;

Route::prefix('risk')->name('risk.')->group(function () {
    Route::get('ui/sanctions', [SanctionCoefficientController::class, 'index'])
        ->name('ui.sanctions');

    Route::get('ui/sanctions/from-incident/{incident}', [SanctionWizardController::class, 'startFromIncident'])
        ->name('ui.sanctions.from-incident');

    Route::get('ui/sanctions/methodology', [SanctionWizardController::class, 'methodology'])
        ->name('ui.sanctions.methodology');

    Route::get('ui/sanctions/wizard/{step?}', [SanctionWizardController::class, 'show'])
        ->whereNumber('step')
        ->name('ui.sanctions.wizard.show');

    Route::post('ui/sanctions/wizard/{step}', [SanctionWizardController::class, 'store'])
        ->whereNumber('step')
        ->name('ui.sanctions.wizard.store');

    Route::post('ui/sanctions/wizard/reset', [SanctionWizardController::class, 'reset'])
        ->name('ui.sanctions.wizard.reset');

    Route::get('ui/sanctions/simulations', [SanctionSimulationController::class, 'index'])
        ->name('ui.sanctions.simulations.index');

    Route::post('ui/sanctions/simulations/current', [SanctionSimulationController::class, 'storeCurrent'])
        ->name('ui.sanctions.simulations.store');

    Route::get('ui/sanctions/simulations/current/report', [SanctionSimulationController::class, 'currentReport'])
        ->name('ui.sanctions.simulations.current-report');

    Route::get('ui/sanctions/simulations/{simulation}', [SanctionSimulationController::class, 'show'])
        ->name('ui.sanctions.simulations.show');

    Route::get('ui/sanctions/simulations/{simulation}/report', [SanctionSimulationController::class, 'report'])
        ->name('ui.sanctions.simulations.report');

    Route::get('sanctions/coefficients', [SanctionCoefficientController::class, 'coefficients'])
        ->name('sanctions.coefficients.index');

    Route::put('sanctions/coefficients', [SanctionCoefficientController::class, 'update'])
        ->name('sanctions.coefficients.update');
});
