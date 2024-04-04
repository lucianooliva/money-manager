<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\EmailVerificationController;

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

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post("/logout", [AuthController::class, 'logout']);
    Route::resource('/expense-category', App\Http\Controllers\ExpenseCategoryController::class);
    Route::post("/expense-category/delete-multiple", [ExpenseCategoryController::class, 'destroyMultiple']);
    Route::resource('/expense', App\Http\Controllers\ExpenseController::class);
    Route::post("/expense/delete-multiple", [ExpenseController::class, 'destroyMultiple']);
    Route::post("/expense/update-done-status-multiple", [ExpenseController::class, 'updateDoneStatusMultiple']);
    Route::get("/expense/category/{categoryId}", [ExpenseController::class, 'getByCategory']);
    Route::resource('/income', App\Http\Controllers\IncomeController::class);
    Route::post("/income/delete-multiple", [IncomeController::class, 'destroyMultiple']);
    Route::get('/email/verify', [EmailVerificationController::class, 'show'])->name('verification.notice');
    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');
    Route::post('/email/send', [EmailVerificationController::class, 'send'])->name('verification.resend');
    Route::get('/expense-category-with-expenses/{userId}', [ExpenseCategoryController::class, 'showWithExpenses']);
});

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::post("/login", [AuthController::class, 'login']);
Route::post("/register", [AuthController::class, 'register']);

Route::patch('/expense/update-all-done', [ExpenseController::class, 'updateAllDone']);
Route::get('/income/total', [IncomeController::class, 'sumValue']);