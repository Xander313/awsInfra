<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Privacy\ProcessingActivityController;


use App\Http\Middleware\SingleSession;


// rutas para auth
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;

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



use App\Http\Middleware\SingleTab;


Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/iniciar-sesion', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/iniciar-sesion', [LoginController::class, 'login'])->name('login.post');

    // Reset de contraseña
    Route::get('/contrasenia/restaurar', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/contrasenia/restaurar', [PasswordResetController::class, 'sendResetCode'])->name('password.reset.send');
    Route::post('/contrasenia/restaurar/confirm', [PasswordResetController::class, 'resetPassword'])->name('password.reset.confirm');

    // Registro (PASO 1: enviar código)
    Route::get('/registrarse', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/registrarse', [RegisterController::class, 'register'])->name('register.post');

    // Verificación (PASO 2: confirmar código y crear usuario)
    Route::get('/verificar-email', [RegisterController::class, 'showVerifyForm'])->name('verify_email.form');
    Route::post('/verificar-email', [RegisterController::class, 'verifyCode'])->name('verify_email.post');

    // (Opcional) reenviar código
    Route::post('/verificar-email/reenviar', [RegisterController::class, 'resendCode'])->name('verify_email.resend');
});


// Logout (solo para usuarios autenticados)
Route::post('/cerrar-sesion', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/forbidden', function () {
        return view('forbidden');
    })->name('forbidden');

    Route::post('/tab/force-claim', function (Request $request) {
        $tabId = $request->header('X-TAB-ID');
        if (!$tabId) {
            return response()->json(['ok' => false, 'error' => 'missing_tab_id'], 422);
        }

        $sessionId = $request->session()->getId();
        $key = "single_tab_session_{$sessionId}";

        Cache::put($key, $tabId, now()->addMinutes(30));

        return response()->json(['ok' => true]);
    })->name('tab.force-claim');

    Route::get('/session/conflict', function (Request $request) {
        if (!$request->session()->has('session_conflict_active')) {
            return redirect()->route('dashboard');
        }
        return view('auth.session_conflict');
    })->name('session.conflict');

    Route::post('/session/takeover', function (Request $request) {
        $user = $request->user();
        $current = $request->session()->getId();
        $key = "single_session_user_{$user->id}";

        $previous = $request->session()->get('session_conflict_active');

        Cache::put($key, $current, now()->addMinutes(30));

        if ($previous && $previous !== $current) {
            try {
                $request->session()->getHandler()->destroy($previous);
            } catch (\Throwable $e) {
                // no-op: si falla, el middleware SingleSession cerrará en el siguiente request
            }
            Cache::forget("single_tab_session_{$previous}");
        }

        $request->session()->forget('session_conflict_active');

        $redirect = redirect()->intended(route('dashboard'))->getTargetUrl();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'redirect' => $redirect]);
        }

        return redirect($redirect);
    })->name('session.takeover');

    Route::middleware([SingleTab::class])->group(function () {
        Route::get('/tab/claim', function () {
            return response()->json(['ok' => true]);
        })->name('tab.claim');

    // Dashboard Routes - SIN middleware
    Route::get('/panel', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/panel/kpis', [DashboardController::class, 'apiKPIs'])->name('dashboard.api.kpis');
    Route::get('/api/panel/alerts', [DashboardController::class, 'apiAlerts'])->name('dashboard.api.alerts');
    Route::get('/api/panel/activity', [DashboardController::class, 'apiRecentActivity'])->name('dashboard.api.activity');

    // ✅ NUEVA RUTA AGREGADA (SOLO ESTA)
    Route::get('/api/panel/modal-data/{type}', [DashboardController::class, 'apiModalData'])->name('dashboard.api.modal-data');

    // RAT Routes
    Route::resource('rat', ProcessingActivityController::class);

    // Org Routes
    Route::prefix('organizaciones')->group(function () {

        Route::post('/check-ruc', [OrgController::class, 'checkRuc'])->name('orgs.check-ruc');

        Route::get('/seleccionar/{org}', function ($orgId) {
            session(['org_id' => $orgId]);
            return redirect()->back()->with('success', 'Organización activada.');
        })->name('orgs.select');

        Route::get('/', [OrgController::class, 'index'])->name('orgs.index');
        Route::get('/crear', [OrgController::class, 'create'])->name('orgs.create');
        Route::post('/', [OrgController::class, 'store'])->name('orgs.store');

        Route::get('/{org}', [OrgController::class, 'show'])->name('orgs.show');
        Route::get('/{org}/editar', [OrgController::class, 'edit'])->name('orgs.edit');
        Route::put('/{org}', [OrgController::class, 'update'])->name('orgs.update');
        Route::delete('/{org}', [OrgController::class, 'destroy'])->name('orgs.destroy');
    });


    // Data Subjects Routes
    Route::prefix('titulares')->group(function () {

        // CRUD titulares (mantiene nombres data-subjects.*)
        Route::get('/', [DataSubjectController::class, 'index'])->name('data-subjects.index');
        Route::get('/crear', [DataSubjectController::class, 'create'])->name('data-subjects.create');
        Route::post('/', [DataSubjectController::class, 'store'])->name('data-subjects.store');

        Route::get('/{dataSubject}', [DataSubjectController::class, 'show'])->name('data-subjects.show');
        Route::get('/{dataSubject}/editar', [DataSubjectController::class, 'edit'])->name('data-subjects.edit');
        Route::put('/{dataSubject}', [DataSubjectController::class, 'update'])->name('data-subjects.update');
        Route::delete('/{dataSubject}', [DataSubjectController::class, 'destroy'])->name('data-subjects.destroy');

        // Consentimientos (anidados al titular)
        Route::get('/{dataSubject}/consentimiento/crear', [DataSubjectController::class, 'createConsent'])
            ->name('data-subjects.consent.create');

        Route::post('/{dataSubject}/consentimiento', [DataSubjectController::class, 'storeConsent'])
            ->name('data-subjects.consent.store');
    });
    // Revocar consentimiento (NO depende del titular en la URL)
    Route::post('/consentimientos/{consent}/revocar', [DataSubjectController::class, 'revokeConsent'])
        ->name('data-subjects.consent.revoke');

    // Risk routes
    require __DIR__.'/risk.php';

    // Audit Routes
    Route::prefix('auditorias')->group(function(){

        // =========================
        // CONTROLES
        // =========================
        Route::prefix('controles')->group(function () {
            Route::get('/', [ControlController::class, 'index'])->name('controls.index');
            Route::get('/crear', [ControlController::class, 'create'])->name('controls.create');
            Route::post('/', [ControlController::class, 'store'])->name('controls.store');
            Route::get('/{control}', [ControlController::class, 'show'])->name('controls.show');
            Route::get('/{control}/editar', [ControlController::class, 'edit'])->name('controls.edit');
            Route::put('/{control}', [ControlController::class, 'update'])->name('controls.update');
            Route::delete('/{control}', [ControlController::class, 'destroy'])->name('controls.destroy');
        });
    
        // =========================
        // HALLAZGOS
        // =========================
        Route::prefix('hallazgos')->group(function () {
            Route::get('/', [AuditFindingController::class, 'index'])->name('findings.index');
            Route::get('/crear', [AuditFindingController::class, 'create'])->name('findings.create');
            Route::post('/', [AuditFindingController::class, 'store'])->name('findings.store');
            Route::get('/{finding}', [AuditFindingController::class, 'show'])->name('findings.show');
            Route::get('/{finding}/editar', [AuditFindingController::class, 'edit'])->name('findings.edit');
            Route::put('/{finding}', [AuditFindingController::class, 'update'])->name('findings.update');
            Route::delete('/{finding}', [AuditFindingController::class, 'destroy'])->name('findings.destroy');
    
            Route::post('/{finding}/cambiar-estado', [AuditFindingController::class, 'changeStatus'])
                ->name('findings.changeStatus');
        });
    
        // =========================
        // ACCIONES CORRECTIVAS
        // =========================
        Route::prefix('acciones-correctivas')->group(function () {
            Route::get('/', [CorrectiveActionController::class, 'index'])->name('corrective_actions.index');
            Route::get('/crear', [CorrectiveActionController::class, 'create'])->name('corrective_actions.create');
            Route::post('/', [CorrectiveActionController::class, 'store'])->name('corrective_actions.store');
        
            Route::get('/{action}', [CorrectiveActionController::class, 'show'])->name('corrective_actions.show');
            Route::get('/{action}/editar', [CorrectiveActionController::class, 'edit'])->name('corrective_actions.edit');
            Route::put('/{action}', [CorrectiveActionController::class, 'update'])->name('corrective_actions.update');
            Route::delete('/{action}', [CorrectiveActionController::class, 'destroy'])->name('corrective_actions.destroy');
        
            Route::post('/{action}/cambiar-estado', [CorrectiveActionController::class, 'changeStatus'])
                ->name('corrective_actions.changeStatus');
        });
        
        
    
        // =========================
        // AUDITORÍAS (SIEMPRE AL FINAL)
        // =========================
        Route::get('/', [AuditController::class, 'index'])->name('audits.index');
        Route::get('/crear', [AuditController::class, 'create'])->name('audits.create');
        Route::post('/', [AuditController::class, 'store'])->name('audits.store');
        Route::get('/{audit}', [AuditController::class, 'show'])->name('audits.show');
        Route::get('/{audit}/editar', [AuditController::class, 'edit'])->name('audits.edit');
        Route::put('/{audit}', [AuditController::class, 'update'])->name('audits.update');
        Route::delete('/{audit}', [AuditController::class, 'destroy'])->name('audits.destroy');
    
        Route::post('/{audit}/cambiar-estado', [AuditController::class, 'changeStatus'])
            ->name('audits.changeStatus');
    });

    // DSAR Routes
    Route::resource('dsar', DsarRequestController::class)->except(['show', 'destroy']);
    Route::post('dsar/{dsar}/evidence', [DsarEvidenceController::class, 'store'])->name('dsar.evidence.store');

    // Privacy Routes


    Route::prefix('privacy')->name('privacy.')->group(function() {
        Route::resource('data_category', DataCategoryController::class);
    });

    // Rutas Fase 2
    Route::post('usuarios/verificar-cedula', [UserController::class, 'verifyCedula'])
        ->name('users.verify_cedula');

    Route::resource('usuarios', UserController::class)
    ->except(['show'])
    ->names([
            'index' => 'users.index',
            'create' => 'users.create',
            'store' => 'users.store',
            'edit' => 'users.edit',
            'update' => 'users.update',
            'destroy' => 'users.destroy'
        ])->parameters(['usuarios' => 'id']);

    Route::get('usuarios/{id}/roles/historial', [UserController::class, 'rolesHistory'])->name('users.roles.history');

    Route::resource('roles', RoleController::class);

    Route::resource('permisos', PermissionController::class)
    ->except(['show'])
    ->names([
        'index' => 'permissions.index',
        'create' => 'permissions.create',
        'store' => 'permissions.store',
        'edit' => 'permissions.edit',
        'update' => 'permissions.update',
        'destroy' => 'permissions.destroy'
    ])->parameters(['permisos' => 'id']);


    //Ruras fase 4

    /*
    |--------------------------------------------------------------------------
    | Sistemas
    |--------------------------------------------------------------------------
    */
    Route::prefix('sistemas')->name('systems.')->group(function () {
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
    Route::prefix('almacenes-datos')->name('data-stores.')->group(function () {
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
    Route::prefix('destinatarios')->name('recipients.')->group(function () {
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
    });
});

Route::get('/seguridad/revelar-clave/{token}', [UserController::class, 'revealPassword'])
    ->name('users.reveal_password')
    ->middleware('signed');
