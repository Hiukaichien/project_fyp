<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\KertasSiasatanController; // Import the controller

Route::get('/', function () {
    return view('auth.login');
});

// Modify the dashboard route to redirect to the Kertas Siasatan index
Route::get('/dashboard', function () {
    return redirect()->route('kertas_siasatan.index'); // Redirect to KS index
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Specific Kertas Siasatan routes should be defined before parameterized ones
    Route::get('/kertas_siasatan/upload', [KertasSiasatanController::class, 'create'])->name('kertas_siasatan.create');
    Route::get('/kertas_siasatan/export-by-project', [KertasSiasatanController::class, 'exportByProject'])->name('kertas_siasatan.export_by_project');

    // General Kertas Siasatan routes
    Route::get('/kertas_siasatan', [KertasSiasatanController::class, 'index'])->name('kertas_siasatan.index');
    Route::post('/kertas_siasatan', [KertasSiasatanController::class, 'store'])->name('kertas_siasatan.store');
    
    // Parameterized Kertas Siasatan routes (these come after more specific ones)
    Route::get('/kertas_siasatan/{kertas_siasatan}', [KertasSiasatanController::class, 'show'])->name('kertas_siasatan.show');
    Route::get('/kertas_siasatan/{kertas_siasatan}/edit', [KertasSiasatanController::class, 'edit'])->name('kertas_siasatan.edit');
    Route::put('/kertas_siasatan/{kertas_siasatan}', [KertasSiasatanController::class, 'update'])->name('kertas_siasatan.update');
    Route::patch('/kertas_siasatan/{kertas_siasatan}', [KertasSiasatanController::class, 'update']);
    Route::delete('/kertas_siasatan/{kertas_siasatan}', [KertasSiasatanController::class, 'destroy'])->name('kertas_siasatan.destroy');

    // Explicit routes for ProjectController
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update'); // Handles PUT requests
    Route::patch('/projects/{project}', [ProjectController::class, 'update']); // Also common to handle PATCH for updates
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // New route for associating a paper with a project
    Route::post('/projects/{project}/associate-paper', [ProjectController::class, 'associatePaper'])->name('projects.associate_paper');
    
    // Route for disassociating a paper from a project
    Route::post('/projects/{project}/disassociate-paper/{paperType}/{paperId}', [ProjectController::class, 'disassociatePaper'])->name('projects.disassociate_paper');

    // New route for downloading associated papers as CSV
    Route::get('projects/{project}/download-csv', [ProjectController::class, 'downloadAssociatedPapersCsv'])->name('projects.download_csv');
  
    // Use Route::resource for shorter.
});

require __DIR__.'/auth.php';
