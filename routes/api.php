<?php

use Illuminate\Support\Facades\Route;
use App\Models\Customer;
use App\Http\Controllers\API\QueryController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\CustomerLogController;

Route::get('/customers', [CustomerController::class, 'index']);
Route::get('/customers/{id}', [CustomerController::class, 'show']);
Route::post('/customers', [CustomerController::class, 'store']);
Route::put('/customers/{id}', [CustomerController::class, 'update']);

// Customer logs
Route::post('/customers/{id}/log', [CustomerLogController::class, 'store']);
Route::get('/customers/{id}/logs', [CustomerLogController::class, 'index']);

Route::post('/sql-query', [QueryController::class, 'executeSQL']);

Route::get('/artisan-clear', function() {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    return 'Caches cleared!';
});
