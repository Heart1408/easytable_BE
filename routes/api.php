<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\AuthController;
use App\Http\Controllers\Staff\ScheduleController;
use App\Http\Controllers\Staff\TableController;
use App\Http\Controllers\Staff\CustomerController;
use App\Http\Controllers\Staff\CategoryController;
use App\Http\Controllers\Staff\ProductController;
use App\Http\Controllers\Staff\FeedbackController;
use App\Http\Controllers\Staff\StaffController;

use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\ConfirmController;

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

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('auth/currentStaff', [AuthController::class, 'getCurrentStaff']);

    Route::group(['prefix' => 'category'], function () {
        Route::post('add', [CategoryController::class, 'add']);
        Route::post('delete/{id}', [CategoryController::class, 'delete']);
        Route::post('edit', [CategoryController::class, 'edit']);
    });

    Route::group(['prefix' => 'product'], function () {
        Route::post('add', [ProductController::class, 'add']);
        Route::post('delete/{id}', [ProductController::class, 'delete']);
    });

    Route::group(['prefix' => 'staff'], function () {
        Route::get('getlist', [StaffController::class, 'get_list']);
        Route::post('add', [StaffController::class, 'create']);
        Route::post('edit', [StaffController::class, 'update']);
        Route::post('delete/{id}', [StaffController::class, 'delete']);
    });
});

Route::middleware(['auth:sanctum', 'role:staff'])->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'schedule'], function () {
        Route::get('getdata/{date}', [ScheduleController::class, 'get_schedule']);
        Route::post('add', [ScheduleController::class, 'create']);
        Route::post('update', [ScheduleController::class, 'update']);
        Route::post('delete', [ScheduleController::class, 'delete']);
    });

    Route::group(['prefix' => 'table'], function () {
        Route::get('getlist/{floor_id}', [TableController::class, 'get_list_table']);
        Route::get('getlistfloor', [TableController::class, 'get_list_floor']);
        Route::get('getStatusCurrent/{table_id}', [TableController::class, 'get_status_current']);
        Route::post('updateStatusBill', [TableController::class, 'update_status_bill']);
        Route::post('paymentConfirm', [TableController::class, 'payment_confirm']);
    });

    Route::group(['prefix' => 'customer'], function () {
        Route::get('getinfo/{booking_id}', [CustomerController::class, 'get_info']);
        Route::get('getlist', [CustomerController::class, 'get_list']);
        Route::get('getBookingInfo', [CustomerController::class, 'get_booking_info']);
    });

    Route::group(['prefix' => 'feedback'], function () {
        Route::get('getlist', [FeedbackController::class, 'get_list']);
        Route::post('changestatus/{id}', [FeedbackController::class, 'change_status']);
    });
});

Route::post('customer/login', [ConfirmController::class, 'login']);
Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
    Route::get('customer/booking', [BookingController::class, 'get_booking']);
    Route::post('customer/addProduct/{dish_id}', [BookingController::class, 'add']);
    Route::post('customer/deleteProduct/{dish_id}', [BookingController::class, 'delete']);
    Route::post('customer/confirmOrder', [BookingController::class, 'confirm_order']);
    Route::post('customer/sendFeedback', [BookingController::class, 'send_feedback']);
});

Route::get('category/getlist', [CategoryController::class, 'get_list']);
Route::get('product/getlist', [ProductController::class, 'get_list']);