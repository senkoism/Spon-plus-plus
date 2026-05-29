<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClassroomBannerController;
use App\Http\Controllers\AssignmentSubmissionController;
use App\Http\Controllers\SectionController;

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Auth
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Classrooms
    Route::get('/classrooms', [ClassroomController::class, 'index'])->name('classrooms.index');
    Route::post('/classrooms', [ClassroomController::class, 'store'])->name('classrooms.store');
    Route::post('/classrooms/join', [ClassroomController::class, 'join'])->name('classrooms.join');
    Route::get('/classrooms/{classroom}', [ClassroomController::class, 'show'])->name('classrooms.show');
    Route::get('/classrooms/{classroom}/edit', [ClassroomController::class, 'edit'])->name('classrooms.edit');
    Route::put('/classrooms/{classroom}', [ClassroomController::class, 'update'])->name('classrooms.update');
    Route::delete('/classrooms/{classroom}', [ClassroomController::class, 'destroy'])->name('classrooms.destroy');
    Route::post('/classrooms/{classroom}/leave', [ClassroomController::class, 'leaveClass'])->name('classrooms.leave');
    Route::delete('/classrooms/{classroom}/members/{user}', [ClassroomController::class, 'kickMember'])->name('classrooms.kick');
    Route::put('/classrooms/{classroom}/members/{user}', [ClassroomController::class, 'updateMember'])->name('classrooms.updateMember');

    // Classroom Banners
    Route::post('/classrooms/{classroom}/banner', [ClassroomBannerController::class, 'update'])->name('classrooms.banner.update');
    Route::delete('/classrooms/{classroom}/banner', [ClassroomBannerController::class, 'destroy'])->name('classrooms.banner.destroy');

    // Stream Items (Announcements / Materials / Assignments)
    Route::post('/classrooms/{classroom}/announcements', [\App\Http\Controllers\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/announcements/{announcement}', [\App\Http\Controllers\AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [\App\Http\Controllers\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::post('/announcements/reorder', [\App\Http\Controllers\AnnouncementController::class, 'reorder'])->name('announcements.reorder');
    Route::get('/announcements/{announcement}/download', [\App\Http\Controllers\AnnouncementController::class, 'download'])->name('announcements.download');
    Route::post('/announcements/{announcement}/submit', [\App\Http\Controllers\AnnouncementController::class, 'submitAssignment'])->name('announcements.submit');

    // Announcement Comments
    Route::post('/announcements/{announcement}/comments', [\App\Http\Controllers\AnnouncementCommentController::class, 'store'])->name('announcements.comments.store');
    Route::put('/comments/{comment}', [\App\Http\Controllers\AnnouncementCommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\AnnouncementCommentController::class, 'destroy'])->name('comments.destroy');
    
    // Legacy mapping (pointing to new stream controllers)
    Route::get('/submissions/{submission}/download', [\App\Http\Controllers\MaterialController::class, 'downloadSubmission'])->name('submissions.download');
    Route::get('/logout-idle', [AuthController::class, 'logoutIdle'])->name('logout.idle');
});
