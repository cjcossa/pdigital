<?php

use App\Http\Controllers\LoanController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SavingController;


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

Route::get('users', [UserController::class, 'index']);
Route::get('users/{id}', [UserController::class, 'show']);
Route::post('users', [UserController::class, 'store']);
Route::put('users/{id}', [UserController::class, 'update']);
Route::delete('users/{id}', [UserController::class, 'delete']);
Route::get('users.group', [UserController::class, 'user']);
Route::get('users.savings', [UserController::class, 'saving']);

Route::get('savings', [SavingController::class, 'index']);
Route::get('savings/{id}', [SavingController::class, 'show']);
Route::post('savings', [SavingController::class, 'store']);
Route::put('savings/{id}', [SavingController::class, 'update']);
Route::delete('savings/{id}', [SavingController::class, 'delete']);
Route::get('savings/balance/{id}', [SavingController::class, 'balance']);
Route::get('savings.total', [SavingController::class, 'total']);
Route::get('savings.users', [SavingController::class, 'saving']);

Route::get('loans', [LoanController::class, 'index']);
Route::get('loans/{id}', [LoanController::class, 'show']);
Route::post('loans', [LoanController::class, 'store']);
Route::put('loans/{id}', [LoanController::class, 'update']);
Route::delete('loans/{id}', [LoanController::class, 'delete']);
Route::get('loans.total', [LoanController::class, 'total']);
Route::get('loans.users', [LoanController::class, 'loan']);
Route::get('loans.topay', [LoanController::class, 'topay']);


Route::get('payments', [PaymentController::class, 'index']);
Route::get('payments/{id}', [PaymentController::class, 'show']);
Route::post('payments', [PaymentController::class, 'store']);
Route::put('payments/{id}', [PaymentController::class, 'update']);
Route::delete('payments/{id}', [PaymentController::class, 'delete']);
Route::get('payments.total', [PaymentController::class, 'total']);
Route::get('payments.loans', [PaymentController::class, 'payment']);
Route::get('payments.loans/{id}', [PaymentController::class, 'remaing']);






