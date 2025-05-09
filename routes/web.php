<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\KertasSiasatanController; // Import the controller

Route::get('/', function () {
    // If using Breeze, this welcome route might be overridden or removed
    // depending on the exact setup. Let's keep it for now but note
    // authenticated users are typically redirected away from it.
    return view('welcome');
});

// Modify the dashboard route to redirect to the Kertas Siasatan index
Route::get('/dashboard', function () {
    return redirect()->route('kertas_siasatan.index'); // Redirect to KS index
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/kertas_siasatan/upload', [KertasSiasatanController::class, 'create'])->name('kertas_siasatan.upload');
    Route::get('/kertas_siasatan/project', [ProjectController::class, 'index'])->name('kertas_siasatan.project');
    
    // Add the resource routes for Kertas Siasatan here
    Route::resource('kertas_siasatan', KertasSiasatanController::class);
    // The route named 'kertas_siasatan.upload' used in the index view
    // corresponds to the 'create' method handled by Route::resource.
});

require __DIR__.'/auth.php';
