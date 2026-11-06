<?php

use Illuminate\Support\Facades\Route;

// Merge these route groups into routes/web.php after adapting controller names.
// The dashboard can remain available to authenticated but unverified users so
// they can see the verification notice. Publishing and provider-management
// actions should require both authentication and verified email.
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('individual')->name('individual.')->group(function () {
        // Route::resource('profile', IndividualProfileController::class)->only(['edit', 'update']);
        // Route::resource('services', IndividualServiceController::class);
    });

    Route::prefix('organization')->name('organization.')->group(function () {
        // Route::resource('profile', OrganizationProfileController::class)->only(['edit', 'update']);
        // Route::resource('services', OrganizationServiceController::class);
        // Route::resource('products', OrganizationProductController::class);
        // Route::resource('branches', OrganizationBranchController::class);
        // Route::resource('jobs', OrganizationJobController::class);
    });
});
