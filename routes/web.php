<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('projects.index'); // List all projects
    Route::get('/show/{id?}', [ProjectController::class, 'create'])->name('projects.create'); // Show a single project (for example)
    Route::put('/restore-multiple', [ProjectController::class, 'restoreMultiple'])->name('projects.restoreMultiple');
    Route::post('/', [ProjectController::class, 'store'])->name('projects.store'); // Create a new project
    Route::put('/{id}', [ProjectController::class, 'update'])->name('projects.update'); // Update an existing project
    Route::delete('delete/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy'); // Delete a project
    Route::get('/recycle-bin', [ProjectController::class, 'recycleBin'])->name('projects.recycle_bin');

});