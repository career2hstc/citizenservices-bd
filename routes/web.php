<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Actions\Fortify\CreateNewUser;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

Route::prefix('individual')->name('individual.')->group(function () {
        // Route::resource('profile', IndividualProfileController::class)->only(['edit', 'update']);
        // Route::resource('services', IndividualServiceController::class);
    });

    Route::prefix('organization')->name('organization.')->group(function () {
         Route::resource('profile', CreateNewUser::class)->only(['edit', 'update']);
         Route::resource('services', CreateNewUser::class);
         Route::resource('products', CreateNewUser::class);
        Route::resource('branches', CreateNewUser::class);
        Route::resource('jobs', CreateNewUser::class);
    });

});


Route::middleware(['auth', 'verified'])->group(function () {
    
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
//require __DIR__.'/provider-protection.example.php';
