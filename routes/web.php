<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DeudorController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DebtorController;
use App\Http\Controllers\AgendaPagosController;

// Agenda de Pagos
Route::get('/agenda-pagos', [AgendaPagosController::class, 'index'])
    ->name('agendas.index');

Route::post('/agenda-pagos/{id}/cambiar-estado', [AgendaPagosController::class, 'cambiarEstado'])
    ->name('agendaspagos.update');

Route::resource('debtors', DebtorController::class);

// CRUD contactos dentro de un deudor
Route::prefix('debtors/{debtor}')->group(function () {
    Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::put('contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
});


Route::get('/deudores', [DeudorController::class, 'index'])->name('deudores.index');
Route::get('/contactos', [ContactController::class, 'index'])->name('contactos.index');


Route::get('/', function () {
    return Inertia::render('Welcome', 
    [
    'status2' => 'kevin',
    'status' => 'kevin'
]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
