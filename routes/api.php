<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupCycleController;
use App\Http\Controllers\GroupUserController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PreUserController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\UserJoinRequestController;

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

Route::middleware('auth:sanctum')->post('/user', function (Request $request) {
    return $request->user();
});

//Route::middleware(['auth:sanctum','checkTokenExpiry'])->get('/me', fn(Request $r) => $r->user());


Route::post('users.auth', [UserController::class, 'authenticate'])->name('login');


Route::post('/pre.users.create', [PreUserController::class, 'store']);
//Route::post('/pre.users.list', [PreUserController::class, 'index']);
Route::middleware(['auth:sanctum', 'checkTokenExpiry'])->group( function () {
    Route::post('/pre.users.list', [PreUserController::class, 'index']);
});

Route::post('/pre.users.update', [PreUserController::class, 'update']);

Route::post('/groups.create', [GroupController::class, 'store']);

Route::post('/roles.create', [RoleController::class, 'store']);

Route::post('/group.cycles.create', [GroupCycleController::class, 'store']);

Route::post('/user.requests.create', [UserJoinRequestController::class, 'store']);

Route::post('/user.requests.update', [UserJoinRequestController::class, 'update']);

Route::post('/group.users.create', [GroupUserController::class, 'store']);







Route::get('users', [UserController::class, 'index']);
Route::get('users/{id}', [UserController::class, 'show']);
Route::post('users.create', [UserController::class, 'store']);
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

Route::prefix('loans')->group(function () {
    Route::get('{loan}/remaining', [LoanController::class, 'remainingAmount'])
        ->whereUuid('loan');

    Route::post('{loan}/payments', [PaymentController::class, 'store'])
        ->whereUuid('loan');

    Route::post('simulate', [LoanController::class, 'simulate']);
});

Route::get('payments', [PaymentController::class, 'index']);
Route::get('payments/{id}', [PaymentController::class, 'show']);
Route::post('payments', [PaymentController::class, 'store']);
Route::put('payments/{id}', [PaymentController::class, 'update']);
Route::delete('payments/{id}', [PaymentController::class, 'delete']);
Route::get('payments.total', [PaymentController::class, 'total']);
Route::get('payments.loans', [PaymentController::class, 'payment']);
Route::get('payments.loans/{id}', [PaymentController::class, 'remaing']);






