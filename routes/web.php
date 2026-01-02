<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Privacy\ProcessingActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Core\OrgController;
use App\Http\Controllers\Privacy\DataSubjectController;
use App\Http\Controllers\Dsar\DsarRequestController;
use App\Http\Controllers\Dsar\DsarEvidenceController;
use App\Http\Controllers\Privacy\DataCategoryController;
use App\Http\Controllers\Audit\AuditController;
use App\Http\Controllers\Audit\ControlController;
use App\Http\Controllers\Audit\AuditFindingController;
use App\Http\Controllers\Audit\CorrectiveActionController;
//rutas fase 2
use App\Http\Controllers\Iam\UserController;
use App\Http\Controllers\Iam\RoleController;
use App\Http\Controllers\Iam\PermissionController;
//Direciones de las rutas de las fase 4 
use App\Http\Controllers\Privacyfase4\SystemController;
use App\Http\Controllers\Privacyfase4\DataStoreController;
use App\Http\Controllers\Privacyfase4\RecipientController;

use App\Http\Controllers\Document\DocumentController;
use App\Http\Controllers\Privacy\CountryController;

Route::get('/', function () {
    return view('core/extencion');
});

// Dashboard Routes - SIN middleware
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/dashboard/kpis', [DashboardController::class, 'apiKPIs'])->name('dashboard.api.kpis');
Route::get('/api/dashboard/alerts', [DashboardController::class, 'apiAlerts'])->name('dashboard.api.alerts');
Route::get('/api/dashboard/activity', [DashboardController::class, 'apiRecentActivity'])->name('dashboard.api.activity');

// ✅ NUEVA RUTA AGREGADA (SOLO ESTA)
Route::get('/api/dashboard/modal-data/{type}', [DashboardController::class, 'apiModalData'])->name('dashboard.api.modal-data');

// RAT Routes
Route::resource('rat', ProcessingActivityController::class);

// Org Routes
Route::post('/orgs/check-ruc', [OrgController::class, 'checkRuc'])->name('orgs.check-ruc');
Route::get('/org/select/{org}', function ($orgId) {
    session(['org_id' => $orgId]);
    return redirect()->back()->with('success', 'Organización activada.');
})->name('orgs.select');
Route::resource('orgs', OrgController::class);

// Data Subjects Routes
Route::resource('data-subjects', DataSubjectController::class);
Route::get('/data-subjects/{dataSubject}/consent/create', [DataSubjectController::class, 'createConsent'])
    ->name('data-subjects.consent.create');
Route::post('/data-subjects/{dataSubject}/consent', [DataSubjectController::class, 'storeConsent'])
    ->name('data-subjects.consent.store');
Route::post('/consent/{consent}/revoke', [DataSubjectController::class, 'revokeConsent'])
    ->name('data-subjects.consent.revoke');

// Risk routes
require __DIR__.'/risk.php';

// Audit Routes
Route::prefix('audit')->group(function(){
    Route::resource('audits', AuditController::class);
    Route::post('audits/{audit}/change-status', [AuditController::class, 'changeStatus'])->name('audits.changeStatus');
    Route::resource('controls', ControlController::class);
    Route::resource('findings', AuditFindingController::class);
    Route::post('/findings/{finding}/change-status', [AuditFindingController::class, 'changeStatus'])->name('findings.changeStatus');
    Route::resource('corrective_actions', CorrectiveActionController::class);
    Route::post('/corrective_actions/{action}/change-status', [CorrectiveActionController::class, 'changeStatus'])
        ->name('corrective_actions.changeStatus');
    
});

// DSAR Routes
Route::resource('dsar', DsarRequestController::class)->except(['show', 'destroy']);
Route::post('dsar/{dsar}/evidence', [DsarEvidenceController::class, 'store'])->name('dsar.evidence.store');

// Privacy Routes


Route::prefix('privacy')->name('privacy.')->group(function() {
    Route::resource('data_category', DataCategoryController::class);
});

// Rutas Fase 2
Route::resource('users', UserController::class);
Route::resource('roles', RoleController::class);
Route::resource('permissions', PermissionController::class);


//Ruras fase 4

/*
|--------------------------------------------------------------------------
| Sistemas
|--------------------------------------------------------------------------
*/
Route::prefix('systems')->name('systems.')->group(function () {
    Route::get('/', [SystemController::class, 'index'])->name('index');
    Route::get('/crear', [SystemController::class, 'create'])->name('create');
    Route::post('/guardar', [SystemController::class, 'store'])->name('store');
    Route::get('/{system}', [SystemController::class, 'show'])->name('show');
    Route::get('/editar/{id}', [SystemController::class, 'edit'])->name('edit');
    Route::put('/actualizar/{id}', [SystemController::class, 'update'])->name('update');
    Route::delete('/eliminar/{id}', [SystemController::class, 'destroy'])->name('destroy');

    // Subrecurso: DataStores por sistema
    Route::get('/{system}/data-stores', [DataStoreController::class, 'indexBySystem'])->name('data-stores.indexBySystem');
});

/*
|--------------------------------------------------------------------------
| Data Stores (CRUD general)
|--------------------------------------------------------------------------
*/
Route::prefix('data-stores')->name('data-stores.')->group(function () {
    Route::get('/', [DataStoreController::class, 'index'])->name('index');
    Route::get('/crear', [DataStoreController::class, 'create'])->name('create');
    Route::post('/guardar', [DataStoreController::class, 'store'])->name('store');
    Route::get('/editar/{id}', [DataStoreController::class, 'edit'])->name('edit');
    Route::put('/actualizar/{id}', [DataStoreController::class, 'update'])->name('update');
    Route::delete('/eliminar/{id}', [DataStoreController::class, 'destroy'])->name('destroy');
});

/*
|--------------------------------------------------------------------------
| Recipients
|--------------------------------------------------------------------------
*/
Route::prefix('recipients')->name('recipients.')->group(function () {
    Route::get('/', [RecipientController::class, 'index'])->name('index');
    Route::get('/crear', [RecipientController::class, 'create'])->name('create');
    Route::post('/guardar', [RecipientController::class, 'store'])->name('store');
    Route::get('/editar/{id}', [RecipientController::class, 'edit'])->name('edit');
    Route::put('/actualizar/{id}', [RecipientController::class, 'update'])->name('update');
    Route::delete('/eliminar/{id}', [RecipientController::class, 'destroy'])->name('destroy');
});

/*
|--------------------------------------------------------------------------
| Document
|--------------------------------------------------------------------------
*/
// Document (SIN auth por ahora)
Route::resource('documents', DocumentController::class);

// Subir nueva versión
Route::get('documents/{document}/versions/create', [DocumentController::class, 'createVersion'])
    ->name('documents.versions.create');

Route::post('documents/{document}/versions', [DocumentController::class, 'storeVersion'])
    ->name('documents.versions.store');

// Activar una versión como principal
Route::post('documents/{document}/versions/{version}/activate', [DocumentController::class, 'activateVersion'])
    ->name('documents.versions.activate');

// Descargar archivo de versión
Route::get('documents/{document}/versions/{version}/download', [DocumentController::class, 'downloadVersion'])
    ->name('documents.versions.download');

// Country
Route::prefix('privacy')->name('privacy.')->group(function() {
    Route::resource('country', CountryController::class);
});
