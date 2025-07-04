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

    // --- NEW AND UPDATED PROJECT ROUTES ---

    // Handles the dynamic import for ALL paper types. This is the new primary import route.
    Route::post('/projects/{project}/import', [ProjectController::class, 'importPapers'])->name('projects.import');
    
    // Handles disassociating ANY paper from a project.
    Route::post('/projects/{project}/disassociate-paper/{paperType}/{paperId}', [ProjectController::class, 'disassociatePaper'])->name('projects.disassociate_paper');
    
    // Handles downloading the aggregated CSV for a project.
    Route::get('/projects/{project}/download-csv', [ProjectController::class, 'downloadAssociatedPapersCsv'])->name('projects.download_csv');

    // --- YAJRA DATATABLES ROUTES (SERVER-SIDE) ---
    // A route is needed for each tab on your project details page.
    Route::post('/projects/{project}/kertas-siasatan-data', [ProjectController::class, 'getKertasSiasatanData'])->name('projects.kertas_siasatan_data');
    Route::post('/projects/{project}/jenayah-papers-data', [ProjectController::class, 'getJenayahPapersData'])->name('projects.jenayah_papers_data');
    Route::post('/projects/{project}/narkotik-papers-data', [ProjectController::class, 'getNarkotikPapersData'])->name('projects.narkotik_papers_data');
    Route::post('/projects/{project}/komersil-papers-data', [ProjectController::class, 'getKomersilPapersData'])->name('projects.komersil_papers_data');
    Route::post('/projects/{project}/trafik-seksyen-papers-data', [ProjectController::class, 'getTrafikSeksyenPapersData'])->name('projects.trafik_seksyen_papers_data');
    Route::post('/projects/{project}/trafik-rule-papers-data', [ProjectController::class, 'getTrafikRulePapersData'])->name('projects.trafik_rule_papers_data');
    Route::post('/projects/{project}/orang-hilang-papers-data', [ProjectController::class, 'getOrangHilangPapersData'])->name('projects.orang_hilang_papers_data');
    Route::post('/projects/{project}/laporan-mati-mengejut-papers-data', [ProjectController::class, 'getLaporanMatiMengejutPapersData'])->name('projects.laporan_mati_mengejut_papers_data');


    // --- STANDARD RESOURCEFUL ROUTES ---

    // Using Route::resource is a cleaner way to define standard CRUD routes.
    // This single line replaces all the individual index, create, store, show, edit, update, and destroy routes.
    Route::resource('projects', ProjectController::class);

    // Individual Kertas Siasatan routes for viewing/editing specific papers.
    // The main list/index is now handled via the project show page.
    Route::resource('kertas_siasatan', KertasSiasatanController::class)->except(['index', 'create']);


    // --- OBSOLETE ROUTES (COMMENTED OUT FOR REFERENCE) ---
    // The functionality of these routes is now handled by ProjectController@importPapers
    // Route::post('/kertas_siasatan', [KertasSiasatanController::class, 'store'])->name('kertas_siasatan.store');
    // Route::post('/projects/{project}/associate-paper', [ProjectController::class, 'associatePaper'])->name('projects.associate_paper');
});

require __DIR__.'/auth.php';