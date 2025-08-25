<?php
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\BreathingExerciseController;
use App\Http\Controllers\PackageNameController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BreathingTemplate;

Route::get('package-names/{name}/products', [ProductController::class, 'index']);
Route::prefix('auth')->controller(UserController::class)->group(function () {
    Route::middleware(['throttle:3,1'])->post('login-otp', 'loginOTP');
    Route::post('check-otp', 'checkOTP');

    Route::post('login', 'login');
    Route::post('register', 'register');

    Route::post('/login-google', 'googleLogin');
    Route::get('/oauth/callback', 'oauthCallback');
    Route::middleware('auth:sanctum')->group(function () {
        Route::put('logout', 'logout');
    });
});
Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('package-names')->controller(PackageNameController::class)->group(function () {
        Route::get('/', 'index');
        Route::middleware('abilities:admin')->post('/', 'store');
        Route::middleware('abilities:admin')->post('/{name}', 'update');

        Route::put('/{name}/try', [UserController::class, 'try']);

        Route::middleware('abilities:admin')->post('/{name}/products', [ProductController::class, 'store']);
        Route::middleware('abilities:admin')->patch('/{name}/products/{id}', [ProductController::class, 'update']);
        Route::middleware('abilities:admin')->delete('/{name}/products/{id}', [ProductController::class, 'delete']);
        Route::put('/{name}/products/{id}/subscribe', [ProductController::class, 'subscribe']);
    });

    Route::post('breathing-exercises', [BreathingExerciseController::class, 'store']);
    Route::get('profile', [BreathingExerciseController::class, 'profile']);

    // ------------------------------------------------
    Route::post('breathing-templates', [BreathingExerciseController::class, 'createTemplate']);
    Route::get('breathing-templates', [BreathingExerciseController::class, 'getTemplates']);
    Route::post('breathing-complete', [BreathingExerciseController::class, 'completeSession']);
    Route::get('profile', [BreathingExerciseController::class, 'profile']);
    Route::put('breathing-templates/{id}', [BreathingExerciseController::class, 'updateTemplate']);
    Route::delete('breathing-templates/{id}', [BreathingExerciseController::class, 'deleteTemplate']);
    Route::get('user-templates', [BreathingExerciseController::class, 'getUserTemplates']);
  });