<?php

use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:sanctum')->group(function () {
    
    // Create invoice for a contract
    Route::post('/contracts/{contract}/invoices', [InvoiceController::class, 'store']);

    // List invoices for a contract
    Route::get('/contracts/{contract}/invoices', [InvoiceController::class, 'index']);

    // Get invoice details with payments
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);

    // Record a payment
    Route::post('/invoices/{invoice}/payments', [InvoiceController::class, 'recordPayment']);

    // Financial summary for a contract
    Route::get('/contracts/{contract}/summary', [InvoiceController::class, 'summary']);
});
