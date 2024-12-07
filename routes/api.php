<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentController;
use App\Http\MiddlewarePipeline;

Route::middleware(MiddlewarePipeline::class)->group(function () {

    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses/approve', [ExpenseController::class, 'approve']);
    Route::post('/payments/manual', [PaymentController::class, 'manualPay']);
});

