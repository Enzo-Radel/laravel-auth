<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/register", [AuthController::class, "register"])
    ->name("register");
Route::post("/login", [AuthController::class, "login"])
    ->name("login");

Route::middleware("auth:sanctum")->group(function() {
    Route::get("/test-route", function() {
        dd("request passed by middleware");
    });
    Route::get("/logout", [AuthController::class, "logout"])->name("logout");
});

Route::get('/email/verify/{id}/{hash}', [AuthController::class, "verifyEmail"])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [AuthController::class, "sendEmailVerification"])->middleware(['auth', 'throttle:6,1'])->name('verification.send');