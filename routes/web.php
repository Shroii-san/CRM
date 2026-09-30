<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\OrganizationChartController;
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
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ==========================
// Protected Web Routes
// ==========================
Route::middleware(['auth', 'permission'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::prefix('api/geographic/bar')->group(function () {
        Route::get('/distribution', [OrganizationChartController::class, 'getGeoDistributionBar']);
        Route::get('/tier/{tier}', [OrganizationChartController::class, 'getTierDetailBar']);
        Route::get('/export', [OrganizationChartController::class, 'exportGeoDataBar']);
    });

    // ==========================
    // PIE CHART ROUTES
    // ==========================
    Route::prefix('api/geographic/pie')->group(function () {
        Route::get('/distribution', [OrganizationChartController::class, 'getGeoDistributionPie']);
        Route::get('/tier/{tier}', [OrganizationChartController::class, 'getTierDetailPie']);
        Route::get('/export', [OrganizationChartController::class, 'exportGeoDataPie']);
    });


    // -----------------------------------------------
    // Domain: Auth & User Management
    // -----------------------------------------------
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/role', [RoleController::class, 'index'])->name('role');
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');

    // -----------------------------------------------
    // Domain: Master Organization & Contact
    // -----------------------------------------------
    // View: pages.company (OrganizationController -> existing blade)
    Route::get('/company', [OrganizationController::class, 'index'])->name('company');
    Route::get('/company/{id}', [OrganizationController::class, 'show'])->name('company.show');

    // View: pages.pic (OrganizationContactController -> existing blade)
    Route::get('/pic', [OrganizationContactController::class, 'index'])->name('pic');

    // -----------------------------------------------
    // Domain: Client Management
    // -----------------------------------------------
    // View: pages.customers (ClientController -> existing blade)
    Route::get('/customers', [ClientController::class, 'index'])->name('customers');

    // -----------------------------------------------
    // Domain: Sales Pipeline & Deals
    // -----------------------------------------------
    // View: pages.pipeline (PipelineController -> existing blade)
    Route::get('/pipeline', [PipelineController::class, 'index'])->name('pipeline');

    // View: pages.transaksi (DealController -> existing blade)
    Route::get('/transaksi', [DealController::class, 'index'])->name('transaksi');

    // View: pages.salesvisit (SalesVisitController -> existing blade)
    Route::get('/salesvisit', [SalesVisitController::class, 'index'])->name('salesvisit');

    // -----------------------------------------------
    // Domain: Task, Communication & Audit
    // -----------------------------------------------
    // View: pages.task (new blade)
    Route::get('/task', [TaskController::class, 'index'])->name('task');

    // View: pages.interaction (new blade)
    Route::get('/interaction', [InteractionController::class, 'index'])->name('interaction');

    // View: pages.note (new blade)
    Route::get('/note', [NoteController::class, 'index'])->name('note');

    // View: pages.activity_log (new blade)
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log');

    // -----------------------------------------------
    // Domain: Integration & Files
    // -----------------------------------------------
    // View: pages.attachment (new blade)
    Route::get('/attachment', [AttachmentController::class, 'index'])->name('attachment');

    // View: pages.external_conversation (new blade)
    Route::get('/external-conversation', [ExternalConversationController::class, 'index'])->name('external-conversation');

    // -----------------------------------------------
    // Calendar (React SPA shell)
    // -----------------------------------------------
    Route::get('/calendar', fn() => view('layout.react'))->name('calendar');
});
