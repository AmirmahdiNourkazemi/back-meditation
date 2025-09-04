<?php
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\BreathingExerciseController;
use App\Http\Controllers\PackageNameController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BreathingTemplate;
use App\Http\Controllers\MoodController;
use App\Http\Controllers\WorryController;
use Illuminate\Support\Facades\Hash;

Route::get('/test-hash', function() {
    $plain = 'amnk1380';
    $hash = Hash::make($plain);

    return [
        'plain' => $plain,
        'hash' => $hash,
        'check' => Hash::check($plain, $hash), // should be true
    ];
});


Route::get('package-names/{name}/products', [ProductController::class, 'index']);
Route::prefix('auth')->controller(UserController::class)->group(function () {
    Route::middleware(['throttle:3,1'])->post('login-otp', 'loginOTP');
    Route::post('check-otp', 'checkOTP');
    Route::post('verify-email-code','verifyEmailCode');
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
        // --------- user profile 
   Route::post('/profile', [UserController::class, 'updateProfile']);
   Route::get('/profile', [UserController::class, 'profile']);


    ///--  mood routes

     Route::post('/moods/today', [MoodController::class, 'storeUserMood']);
     Route::get('/moods/history', [MoodController::class, 'getUserMoods']);
    

    // --------- get moods  
    Route::get('/moods', [MoodController::class, 'getAllMoods']);
    
    // Route::post('breathing-exercises', [BreathingExerciseController::class, 'store']);
    // Route::get('profile', [BreathingExerciseController::class, 'profile']);

    // ------------------------------------------------
    Route::post('breathing-templates', [BreathingExerciseController::class, 'createTemplate']);
    Route::get('breathing-templates', [BreathingExerciseController::class, 'getTemplates']);
    Route::post('breathing-complete', [BreathingExerciseController::class, 'completeSession']);
    // Route::get('profile', [BreathingExerciseController::class, 'profile']);
    Route::put('breathing-templates/{id}', [BreathingExerciseController::class, 'updateTemplate']);
    Route::delete('breathing-templates/{id}', [BreathingExerciseController::class, 'deleteTemplate']);
    Route::get('user-templates', [BreathingExerciseController::class, 'getUserTemplates']);
    Route::get('breathing-sessions', [BreathingExerciseController::class, 'getSessions']);

    /// worry box feature
    Route::prefix('worries')->controller(WorryController::class)->group(function () {
        Route::post('/', 'store');            // Create worry + note
        Route::get('/', 'index');             // Get all worries
        Route::put('/{id}', 'update');        // Edit worry & note
        Route::patch('/{id}/toggle', 'toggleSolved'); // Mark solved / unsolved
        Route::delete('/{id}', 'destroy');    // Delete worry
    });

  });