<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\AuthController;
use App\Http\Controllers\Staff\ScheduleController;
use App\Http\Controllers\Staff\TableController;
use App\Http\Controllers\Staff\CustomerController;
use App\Http\Controllers\Staff\CategoryController;
use App\Http\Controllers\Staff\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/currentStaff', [AuthController::class, 'getCurrentStaff']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'schedule'], function () {
        Route::get('getdata/{date}', [ScheduleController::class, 'get_schedule']);
        Route::post('add', [ScheduleController::class, 'create']);
        Route::post('delete', [ScheduleController::class, 'delete']);
    });

    Route::group(['prefix' => 'table'], function () {
        Route::get('getlist/{floor_id}', [TableController::class, 'get_list_table']);
        Route::get('getlistfloor', [TableController::class, 'get_list_floor']);
        Route::get('getStatusCurrent/{table_id}', [TableController::class, 'get_status_current']);
    });

    Route::group(['prefix' => 'customer'], function () {
        Route::get('getinfo/{booking_id}', [CustomerController::class, 'get_info']);
    });

    Route::group(['prefix' => 'category'], function () {
        Route::get('getlist', [CategoryController::class, 'get_list']);
        Route::post('add', [CategoryController::class, 'add']);
        Route::post('delete/{id}', [CategoryController::class, 'delete']);
        Route::post('edit', [CategoryController::class, 'edit']);
    });

    Route::group(['prefix' => 'product'], function () {
        Route::get('getlist', [ProductController::class, 'get_list']);
    });
});