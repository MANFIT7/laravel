<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesController;

Route::get('/', function () {
    return redirect()->route('sales.index');
});

Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
Route::get('/sales/list', [SalesController::class, 'list'])->name('sales.list');
Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
Route::get('/sales/{id}', [SalesController::class, 'show'])->name('sales.show');
Route::put('/sales/{id}', [SalesController::class, 'update'])->name('sales.update');
Route::delete('/sales/{id}', [SalesController::class, 'destroy'])->name('sales.destroy');
