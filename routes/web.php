<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Fremaa_jobsController;
use App\Http\Controllers\HomeController;
use App\Models\Fremaa_job;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [Fremaa_jobsController::class, 'index'])->name('jobs');
Route::get('/jobs/detail/{id}', [Fremaa_jobsController::class, 'details'])->name('jobDetail');
Route::post('/apply-job', [Fremaa_jobsController::class, 'applyJob'])->name('applyJob');
Route::post('/save-job', [Fremaa_jobsController::class, 'saveJob'])->name('saveJob');


// Route::get('/account/login', [AccountController::class, 'login'])->name('account.login');
// Route::post('/account/authenticate', [AccountController::class, 'authenticate'])->name('account.authenticate');
// Route::get('/account/register', [AccountController::class, 'registration'])->name('account.registration');
// Route::post('/account/process-register', [AccountController::class, 'proceesRegistration'])->name('account.processRegistration');

// Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile')->middleware([Authenticate::class]);
// Route::get('/account/logout', [AccountController::class, 'logout'])->name('account.logout');

// Guest-only routes
Route::middleware('guest')->group(function () {
    Route::get('/account/register', [AccountController::class, 'registration'])->name('account.registration');
    Route::post('/account/process-register', [AccountController::class, 'proceesRegistration'])->name('account.processRegistration');
    Route::get('/account/login', [AccountController::class, 'login'])->name('account.login');
    Route::post('/account/authenticate', [AccountController::class, 'authenticate'])->name('account.authenticate');
});

// Authenticated-only routes
Route::middleware('auth')->group(function () {
    Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
    Route::put('/account/update-profile', [AccountController::class, 'updateProfile'])->name('account.updateProfile');
    Route::get('/account/logout', [AccountController::class, 'logout'])->name('account.logout');
    Route::post('/account/update-profile-pic', [AccountController::class, 'updateProfilePic'])->name('account.updateProfilePic');
    Route::get('/account/create-job', [AccountController::class, 'createJob'])->name('account.createJob');
    Route::post('/account/save-job', [AccountController::class, 'saveJob'])->name('account.saveJob');
    Route::get('/account/my-jobs', [AccountController::class, 'myJobs'])->name('account.myJobs');
    Route::get('/account/my-jobs/edit/{jobId}', [AccountController::class, 'editJob'])->name('account.editJob');
    Route::post('/account/update-job/{jobId}', [AccountController::class, 'updateJob'])->name('account.updateJob');
    Route::post('/account/delete-job', [AccountController::class, 'deleteJob'])->name('account.deleteJob');
    Route::get('/account/my-jobs-applications', [AccountController::class, 'myJobApplications'])->name('account.myJobApplications');
    Route::post('/account/remove-job-application', [AccountController::class, 'removeJobs'])->name('account.removeJobs');
    Route::get('/account/saved-jobs', [AccountController::class, 'savedJobs'])->name('account.savedJobs');
    Route::post('/account/remove-saved-job', [AccountController::class, 'removeSavedJob'])->name('account.removeSavedJob');
});
