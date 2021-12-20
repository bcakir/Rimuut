<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CurrencyController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\StatusController;
use App\Http\Controllers\API\UserController;


Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signup']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', [AuthController::class, 'logout']);

        // Invoice Routes
        Route::group(['prefix' => 'invoices'], function () {
            Route::get('/', [InvoiceController::class, 'index']);
            Route::get('/{id}/show', [InvoiceController::class, 'show'])->where('id', '[0-9]+');
            Route::post('create', [InvoiceController::class, 'store']);
            Route::patch('assign', [InvoiceController::class, 'assign']);
            Route::get('expired', [InvoiceController::class, 'expired']);
        });

        // Extra endpoints to show test data
        Route::get('users', [UserController::class, 'index']);
        Route::get('roles', [RoleController::class, 'index']);
        Route::get('statuses', [StatusController::class, 'index']);
        Route::get('currencies', [CurrencyController::class, 'index']);
    });
});