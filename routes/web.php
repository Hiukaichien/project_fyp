<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\KertasSiasatanController;

Route::get('/', function () {
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

    // Project-specific routes
    Route::post('/projects/{project}/import', [ProjectController::class, 'importPapers'])->name('projects.import');
    Route::post('/projects/{project}/disassociate-paper/{paperType}/{paperId}', [ProjectController::class, 'disassociatePaper'])->name('projects.disassociate_paper');
    
    Route::get('/projects/{project}/export', [ProjectController::class, 'exportPapers'])->name('projects.export_papers');

    // DataTables routes
    Route::post('/projects/{project}/kertas-siasatan-data', [ProjectController::class, 'getKertasSiasatanData'])->name('projects.kertas_siasatan_data');
    Route::post('/projects/{project}/jenayah-papers-data', [ProjectController::class, 'getJenayahPapersData'])->name('projects.jenayah_papers_data');
    Route::post('/projects/{project}/narkotik-papers-data', [ProjectController::class, 'getNarkotikPapersData'])->name('projects.narkotik_papers_data');
    Route::post('/projects/{project}/komersil-papers-data', [ProjectController::class, 'getKomersilPapersData'])->name('projects.komersil_papers_data');
    Route::post('/projects/{project}/trafik-seksyen-papers-data', [ProjectController::class, 'getTrafikSeksyenPapersData'])->name('projects.trafik_seksyen_papers_data');
    Route::post('/projects/{project}/trafik-rule-papers-data', [ProjectController::class, 'getTrafikRulePapersData'])->name('projects.trafik_rule_papers_data');
    Route::post('/projects/{project}/orang-hilang-papers-data', [ProjectController::class, 'getOrangHilangPapersData'])->name('projects.orang_hilang_papers_data');
    Route::post('/projects/{project}/laporan-mati-mengejut-papers-data', [ProjectController::class, 'getLaporanMatiMengejutPapersData'])->name('projects.laporan_mati_mengejut_papers_data');

    // Resourceful routes for Projects
    Route::resource('projects', ProjectController::class);

    // Generic routes for all paper types, handled by KertasSiasatanController
    Route::get('/papers/{paperType}/{id}', [KertasSiasatanController::class, 'show'])->name('kertas_siasatan.show');
    Route::get('/papers/{paperType}/{id}/edit', [KertasSiasatanController::class, 'edit'])->name('kertas_siasatan.edit');
    Route::put('/papers/{paperType}/{id}', [KertasSiasatanController::class, 'update'])->name('kertas_siasatan.update');
    
    // Specific destroy route for KertasSiasatan model
    Route::delete('/kertas_siasatan/{kertasSiasatan}', [KertasSiasatanController::class, 'destroy'])->name('kertas_siasatan.destroy');
});

require __DIR__.'/auth.php';