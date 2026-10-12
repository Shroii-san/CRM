<?php

use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

// Auth Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;

// Web View Controllers
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\RoleController;
use App\Http\Controllers\Web\MenuController;
use App\Http\Controllers\Web\OrganizationController;
use App\Http\Controllers\Web\OrganizationContactController;
use App\Http\Controllers\Web\ClientController;
use App\Http\Controllers\Web\PipelineController;
use App\Http\Controllers\Web\DealController;
use App\Http\Controllers\Web\SalesVisitController;
use App\Http\Controllers\Web\TaskController;
use App\Http\Controllers\Web\InteractionController;
use App\Http\Controllers\Web\NoteController;
use App\Http\Controllers\Web\ActivityLogController;
use App\Http\Controllers\Web\AttachmentController;
use App\Http\Controllers\Web\ExternalConversationController;

// ==========================
// Public Routes (Guest Only)
// ==========================
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])
        ->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPasswordForm'])
        ->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
        ->name('password.update');
});

// ==========================
// Auth Actions (Non-Guest)
// ==========================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ==========================
// Protected Web Routes
// ==========================
Route::middleware(['auth', 'permission'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // -----------------------------------------------
    // Domain: Auth & User Management
    // -----------------------------------------------
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/role', [RoleController::class, 'index'])->name('role');
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');

    // -----------------------------------------------
    // Domain: Master Organization & Contact
    // -----------------------------------------------
    Route::prefix('organization')->name('organization.')->group(function () {
        // Halaman & data
        Route::get('/', [OrganizationController::class, 'index'])->name('index');
        Route::get('/search', [OrganizationController::class, 'search'])->name('search');
        Route::post('/', [OrganizationController::class, 'store'])->name('store');
        Route::get('/{id}', [OrganizationController::class, 'show'])->name('show');
        Route::get('/{id}/pics', [OrganizationController::class, 'pics'])->name('pics');
        Route::put('/{id}', [OrganizationController::class, 'update'])->name('update');
        Route::delete('/{id}', [OrganizationController::class, 'destroy'])->name('destroy');

        // Export PDF & Excel
        Route::get('/export/pdf-list', [OrganizationController::class, 'exportPdfList'])->name('pdf.list');
        Route::get('/export/pdf-selected', [OrganizationController::class, 'exportPdfSelected'])->name('pdf.selected');
        Route::get('/export/pivot-excel', [OrganizationController::class, 'exportPivotExcel'])->name('pivot.excel');
        Route::get('/{id}/pdf', [OrganizationController::class, 'exportPdf'])->name('pdf');
    });

    // View: pages.pic (OrganizationContactController -> existing blade)
    Route::get('/pic', [OrganizationContactController::class, 'index'])->name('pic');

    // -----------------------------------------------
    // Domain: Client Management
    // -----------------------------------------------
    Route::get('/clients', [ClientController::class, 'index'])->name('clients');
    Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::get('/clients/export', [ClientController::class, 'export'])->name('clients.export');

    // -----------------------------------------------
    // Domain: Sales Pipeline & Deals
    // -----------------------------------------------
    Route::get('/pipeline', [PipelineController::class, 'index'])->name('pipeline');
    Route::get('/transaksi', [DealController::class, 'index'])->name('transaksi');
    Route::get('/salesvisit', [SalesVisitController::class, 'index'])->name('salesvisit');

    // -----------------------------------------------
    // Domain: Task, Communication & Audit
    // -----------------------------------------------
    Route::get('/task', [TaskController::class, 'index'])->name('task');
    Route::get('/interaction', [InteractionController::class, 'index'])->name('interaction');
    Route::get('/note', [NoteController::class, 'index'])->name('note');
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log');

    // -----------------------------------------------
    // Domain: Integration & Files
    // -----------------------------------------------
    Route::get('/attachment', [AttachmentController::class, 'index'])->name('attachment');
    Route::get('/external-conversation', [ExternalConversationController::class, 'index'])->name('external-conversation');

    // -----------------------------------------------
    // Calendar (React SPA shell)
    // -----------------------------------------------
    Route::get('/calendar', fn() => view('layouts.react'))->name('calendar');
});
