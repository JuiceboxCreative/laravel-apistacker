<?php
use App\Http\Controllers\SanctumController;

Route::post('/sanctum/token', [SanctumController::class, 'token']);

Route::fallback(function(){
    return response()->json(['message' => 'Request not found'], 404);
});
