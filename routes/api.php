<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//route bisa diakses tanpa login
Route::post('/login', [AuthController::class, 'login']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/orders', [OrderController::class, 'index']); 
Route::post('/orders', [OrderController::class, 'store']);
Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
Route::post('/orders/{id}/pay', [PaymentController::class, 'createQris']);
Route::post('/xendit/callback', [PaymentController::class, 'webhook']);
Route::get('/orders/{id}/status', [PaymentController::class, 'checkStatus']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::put('/orders/{id}/payment', [OrderController::class, 'updatePayment']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

//route untuk mendapatkan data user yang sedang login
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); 