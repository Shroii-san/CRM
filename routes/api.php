<?php

use Illuminate\Support\Facades\Route;

// API V1 Controllers
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\OrganizationController;
use App\Http\Controllers\Api\V1\IndustryController;
use App\Http\Controllers\Api\V1\OrganizationContactController;
use App\Http\Controllers\Api\V1\OrganizationSocialProfileController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\ClientSourceController;
use App\Http\Controllers\Api\V1\PipelineController;
use App\Http\Controllers\Api\V1\StageTaskTemplateController;
use App\Http\Controllers\Api\V1\DealController;
use App\Http\Controllers\Api\V1\SalesVisitController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\InteractionController;
use App\Http\Controllers\Api\V1\NoteController;
use App\Http\Controllers\Api\V1\ActivityLogController;
use App\Http\Controllers\Api\V1\AttachmentController;
use App\Http\Controllers\Api\V1\ExternalConversationController;
use App\Http\Controllers\Api\V1\RegionController;
use App\Http\Controllers\AuthController;

// ==========================
// REST API V1
// ==========================

// Auth routes (publik, tidak butuh token)
Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::prefix('v1')->name('api.v1.')->middleware(['auth:sanctum,web'])->group(function () {

    // ------------------------------
    // Domain: Auth & User Management
    // ------------------------------
    Route::apiResource('users', UserController::class)->except(['create', 'edit', 'show']);

    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::put('/{id}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/assign-permissions', [RoleController::class, 'assignPermissions'])->name('assignPermissions');
    });

    Route::prefix('menus')->name('menus.')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::get('/tree', [MenuController::class, 'tree'])->name('tree');
        Route::post('/', [MenuController::class, 'store'])->name('store');
        Route::put('/{id}', [MenuController::class, 'update'])->name('update');
        Route::delete('/{id}', [MenuController::class, 'destroy'])->name('destroy');
    });

    // -----------------------------------------------
    // Domain: Master Organization, Contact & Industry
    // -----------------------------------------------
    Route::prefix('organizations')->name('organizations.')->group(function () {
        Route::get('/', [OrganizationController::class, 'index'])->name('index');
        Route::get('/dropdown', [OrganizationController::class, 'dropdown'])->name('dropdown');
        Route::get('/{id}', [OrganizationController::class, 'show'])->name('show')->whereNumber('id');
        Route::post('/', [OrganizationController::class, 'store'])->name('store');
        Route::put('/{id}', [OrganizationController::class, 'update'])->name('update')->whereNumber('id');
        Route::delete('/{id}', [OrganizationController::class, 'destroy'])->name('destroy')->whereNumber('id');

        // Sub-resource: Organization Contacts (PICs)
        Route::get('/{organizationId}/contacts', [OrganizationContactController::class, 'byOrganization'])->name('contacts.byOrganization');
    });

    Route::prefix('organization-contacts')->name('organization-contacts.')->group(function () {
        Route::get('/', [OrganizationContactController::class, 'index'])->name('index');
        Route::post('/', [OrganizationContactController::class, 'store'])->name('store');
        Route::put('/{id}', [OrganizationContactController::class, 'update'])->name('update');
        Route::delete('/{id}', [OrganizationContactController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('organization-social-profiles')->name('organization-social-profiles.')->group(function () {
        Route::get('/', [OrganizationSocialProfileController::class, 'index'])->name('index');
        Route::post('/', [OrganizationSocialProfileController::class, 'store'])->name('store');
        Route::put('/{id}', [OrganizationSocialProfileController::class, 'update'])->name('update');
        Route::delete('/{id}', [OrganizationSocialProfileController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('industries')->name('industries.')->group(function () {
        Route::get('/', [IndustryController::class, 'index'])->name('index');
        Route::post('/', [IndustryController::class, 'store'])->name('store');
        Route::put('/{id}', [IndustryController::class, 'update'])->name('update');
        Route::delete('/{id}', [IndustryController::class, 'destroy'])->name('destroy');
    });

    // ----------------------------------
    // Domain: Client Management
    // ----------------------------------
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::put('/{id}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{id}', [ClientController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [ClientController::class, 'bulkDelete'])->name('bulkDelete');
    });

    Route::prefix('client-sources')->name('client-sources.')->group(function () {
        Route::get('/', [ClientSourceController::class, 'index'])->name('index');
        Route::post('/', [ClientSourceController::class, 'store'])->name('store');
        Route::put('/{id}', [ClientSourceController::class, 'update'])->name('update');
        Route::delete('/{id}', [ClientSourceController::class, 'destroy'])->name('destroy');
    });

    // ----------------------------------
    // Domain: Sales Pipeline & Deals
    // ----------------------------------
    Route::prefix('pipelines')->name('pipelines.')->group(function () {
        Route::get('/', [PipelineController::class, 'index'])->name('index');
        Route::post('/', [PipelineController::class, 'store'])->name('store');
        Route::get('/{id}', [PipelineController::class, 'show'])->name('show');
        Route::put('/{id}', [PipelineController::class, 'update'])->name('update');
        Route::delete('/{id}', [PipelineController::class, 'destroy'])->name('destroy');

        // Sub-resource: Pipeline Stages
        Route::post('/{pipelineId}/stages', [PipelineController::class, 'storeStage'])->name('stages.store');
        Route::post('/{pipelineId}/stages/reorder', [PipelineController::class, 'reorderStages'])->name('stages.reorder');
    });

    Route::prefix('stages')->name('stages.')->group(function () {
        Route::put('/{stageId}', [PipelineController::class, 'updateStage'])->name('update');
        Route::delete('/{stageId}', [PipelineController::class, 'destroyStage'])->name('destroy');
    });

    Route::prefix('stage-task-templates')->name('stage-task-templates.')->group(function () {
        Route::get('/', [StageTaskTemplateController::class, 'index'])->name('index');
        Route::post('/', [StageTaskTemplateController::class, 'store'])->name('store');
        Route::get('/{id}', [StageTaskTemplateController::class, 'show'])->name('show');
        Route::put('/{id}', [StageTaskTemplateController::class, 'update'])->name('update');
        Route::delete('/{id}', [StageTaskTemplateController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('deals')->name('deals.')->group(function () {
        Route::get('/', [DealController::class, 'index'])->name('index');
        Route::post('/', [DealController::class, 'store'])->name('store');
        Route::get('/{id}', [DealController::class, 'show'])->name('show');
        Route::put('/{id}', [DealController::class, 'update'])->name('update');
        Route::delete('/{id}', [DealController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/move-stage', [DealController::class, 'moveStage'])->name('moveStage');
    });

    Route::prefix('sales-visits')->name('sales-visits.')->group(function () {
        Route::get('/', [SalesVisitController::class, 'index'])->name('index');
        Route::post('/', [SalesVisitController::class, 'store'])->name('store');
        Route::get('/{id}', [SalesVisitController::class, 'show'])->name('show');
        Route::put('/{id}', [SalesVisitController::class, 'update'])->name('update');
        Route::delete('/{id}', [SalesVisitController::class, 'destroy'])->name('destroy');
    });

    // ----------------------------------
    // Domain: Task, Communication & Audit
    // ----------------------------------
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/{id}', [TaskController::class, 'show'])->name('show');
        Route::put('/{id}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{id}', [TaskController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/toggle-complete', [TaskController::class, 'toggleComplete'])->name('toggleComplete');
        Route::post('/{id}/reminders', [TaskController::class, 'addReminder'])->name('reminders.store');
    });

    Route::prefix('interactions')->name('interactions.')->group(function () {
        Route::get('/', [InteractionController::class, 'index'])->name('index');
        Route::post('/', [InteractionController::class, 'store'])->name('store');
        Route::get('/{id}', [InteractionController::class, 'show'])->name('show');
        Route::put('/{id}', [InteractionController::class, 'update'])->name('update');
        Route::delete('/{id}', [InteractionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('notes')->name('notes.')->group(function () {
        Route::get('/', [NoteController::class, 'index'])->name('index');
        Route::post('/', [NoteController::class, 'store'])->name('store');
        Route::get('/{id}', [NoteController::class, 'show'])->name('show');
        Route::put('/{id}', [NoteController::class, 'update'])->name('update');
        Route::delete('/{id}', [NoteController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/{id}', [ActivityLogController::class, 'show'])->name('show');
    });

    // ----------------------------------
    // Domain: Integration & Files
    // ----------------------------------
    Route::prefix('attachments')->name('attachments.')->group(function () {
        Route::get('/', [AttachmentController::class, 'index'])->name('index');
        Route::post('/', [AttachmentController::class, 'store'])->name('store');
        Route::get('/{id}', [AttachmentController::class, 'show'])->name('show');
        Route::put('/{id}', [AttachmentController::class, 'update'])->name('update');
        Route::delete('/{id}', [AttachmentController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('external-conversations')->name('external-conversations.')->group(function () {
        Route::get('/', [ExternalConversationController::class, 'index'])->name('index');
        Route::post('/', [ExternalConversationController::class, 'store'])->name('store');
        Route::get('/{id}', [ExternalConversationController::class, 'show'])->name('show');
        Route::put('/{id}', [ExternalConversationController::class, 'update'])->name('update');
        Route::delete('/{id}', [ExternalConversationController::class, 'destroy'])->name('destroy');
    });

    // ----------------------------------
    // Domain: Reference Data (Wilayah)
    // ----------------------------------
    Route::prefix('regions')->name('regions.')->group(function () {
        Route::get('/countries', [RegionController::class, 'countries'])->name('countries');
        Route::get('/provinces', [RegionController::class, 'provinces'])->name('provinces');
        Route::get('/provinces/{provinceId}/regencies', [RegionController::class, 'regencies'])->name('regencies');
        Route::get('/regencies/{regencyId}/districts', [RegionController::class, 'districts'])->name('districts');
        Route::get('/districts/{districtId}/villages', [RegionController::class, 'villages'])->name('villages');
    });
});
