<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\KertasSiasatanController;
use App\Http\Controllers\AdminUserController;

Route::get('/', function () {
    // If user is already authenticated, redirect to dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return redirect()->route('projects.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class);
    });

    // Project-specific routes
    Route::post('/projects/{project}/import', [ProjectController::class, 'importPapers'])->name('projects.import');
    Route::delete('/projects/{project}/destroy-paper/{paperType}/{paperId}', [ProjectController::class, 'destroyPaper'])->name('projects.destroy_paper');
    Route::get('/projects/{project}/export', [ProjectController::class, 'exportPapers'])->name('projects.export_papers');
    Route::get('/templates/{filename}', [ProjectController::class, 'downloadTemplate'])->name('templates.download');

    Route::delete('/projects/{project}/destroy-all-papers', [ProjectController::class, 'destroyAllPapers'])
    ->name('projects.destroy_all_papers')
    ->middleware(['auth']);

    Route::get('/projects/{project}/get-papers-for-destroy', [ProjectController::class, 'getPapersForDestroy'])
    ->name('projects.get_papers_for_destroy')
    ->middleware(['auth']);

    Route::post('/projects/{project}/destroy-selected-papers', [ProjectController::class, 'destroySelectedPapers'])
    ->name('projects.destroy_selected_papers')
    ->middleware(['auth']);

    // --- STEP 1: Update all DataTables routes ---
    Route::post('/projects/{project}/jenayah-data', [ProjectController::class, 'getJenayahData'])->name('projects.jenayah_data');
    Route::post('/projects/{project}/narkotik-data', [ProjectController::class, 'getNarkotikData'])->name('projects.narkotik_data');
    Route::post('/projects/{project}/komersil-data', [ProjectController::class, 'getKomersilData'])->name('projects.komersil_data');
    Route::post('/projects/{project}/trafik-seksyen-data', [ProjectController::class, 'getTrafikSeksyenData'])->name('projects.trafik_seksyen_data');
    Route::post('/projects/{project}/trafik-rule-data', [ProjectController::class, 'getTrafikRuleData'])->name('projects.trafik_rule_data');
    Route::post('/projects/{project}/orang-hilang-data', [ProjectController::class, 'getOrangHilangData'])->name('projects.orang_hilang_data');
    Route::post('/projects/{project}/laporan-mati-mengejut-data', [ProjectController::class, 'getLaporanMatiMengejutData'])->name('projects.laporan_mati_mengejut_data');

    // Resourceful routes for Projects
    Route::resource('projects', ProjectController::class);

    // Generic routes for all paper types, handled by KertasSiasatanController
    Route::get('/papers/{paperType}/{id}', [KertasSiasatanController::class, 'show'])->name('kertas_siasatan.show');
    Route::get('/papers/{paperType}/{id}/edit', [KertasSiasatanController::class, 'edit'])->name('kertas_siasatan.edit');
    Route::put('/papers/{paperType}/{id}', [KertasSiasatanController::class, 'update'])->name('kertas_siasatan.update');
    
});

require __DIR__.'/auth.php';