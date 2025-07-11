<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Utilizing Fortify endpoints
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;

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

// Login
Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:auth.login')->name('api.auth.login');

// Logout
Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('api.auth.logout');

// Password Reset
if (Features::enabled(Features::resetPasswords())) {
    Route::middleware(['throttle:auth'])->group(function () {
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store']);
        Route::post('reset-password', [NewPasswordController::class, 'store'])->name('api.password.update');
    });
}

// Once we got a token that has all the access, give the user access
Route::middleware(['auth:sanctum', 'token:*'])->group(function () {

    // Two Factor Authentication after authentication
    if (Features::enabled(Features::twoFactorAuthentication())) {
        Route::post('user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
            ->name('api.two-factor.enable');

        Route::post('user/confirmed-two-factor-authentication', [ConfirmedTwoFactorAuthenticationController::class, 'store'])
            ->name('api.two-factor.confirm');

        Route::delete('user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
            ->name('api.two-factor.disable');

        Route::get('user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
            ->name('api.two-factor.qr-code');

        Route::get('user/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])
            ->name('api.two-factor.recovery-codes');

        Route::post('user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store']);
    }

    // Revoke all current tokens for the user
    Route::post('auth/revoke-tokens', [AuthController::class, 'revokeTokens'])->name('auth.revoke-tokens');

    // Crud API Routes placeholder
});

Route::fallback(function () {
    return response()->json(['message' => 'Request not found'], 404);
});
