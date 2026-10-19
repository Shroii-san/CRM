<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hrd\StoreCandidateRequest;
use App\Http\Requests\Hrd\UpdateCandidateRequest;
use App\Http\Resources\Hrd\CandidateResource;
use App\Models\Candidate;
use App\Services\HrdService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class HrdController extends Controller
{
    public function __construct(protected HrdService $hrdService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = array_filter([
            'status' => $request->filled('status') ? (int) $request->input('status') : null,
            'division' => $request->filled('division') ? $request->input('division') : null,
        ], fn($value) => $value !== null && $value !== '');

        $candidates = $this->hrdService->list(
            search: $request->input('search'),
            filters: $filters,
            perPage: (int) $request->input('per_page', 15),
            page: (int) $request->input('page', 1),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDir: $request->input('sort_dir', 'desc'),
        );

        return $this->paginatedResponse(
            $candidates,
            CandidateResource::class,
            'Candidate list retrieved successfully'
        );
    }

    public function show(int $id): JsonResponse
    {
        $candidate = $this->hrdService->findWithDetails($id);

        return $this->successResponse(
            CandidateResource::make($candidate),
            'Candidate details retrieved successfully'
        );
    }

    public function store(StoreCandidateRequest $request): JsonResponse
    {
        try {
            $data = array_merge($request->validated(), $request->allFiles());
            $candidate = $this->hrdService->create($data);

            return $this->successResponse(
                CandidateResource::make($candidate->fresh(['educationHistories', 'families', 'employee', 'attachments'])),
                'Candidate created successfully',
                201
            );
        } catch (Throwable $e) {
            Log::error('Failed to store candidate via API', [
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Candidate data could not be saved.', 500);
        }
    }

    public function update(UpdateCandidateRequest $request, int $id): JsonResponse
    {
        try {
            $data = array_merge($request->validated(), $request->allFiles());
            $candidate = $this->hrdService->update($id, $data);

            if ((int) $candidate->fresh()->status === Candidate::STATUS_ACCEPTED) {
                $this->hrdService->promoteCandidateToEmployee($candidate);
            }

            return $this->successResponse(
                CandidateResource::make($candidate->fresh(['educationHistories', 'families', 'employee', 'attachments'])),
                'Candidate updated successfully'
            );
        } catch (Throwable $e) {
            Log::error('Failed to update candidate via API', [
                'candidate_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse('Candidate data could not be updated.', 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->hrdService->delete($id);

            return $this->successResponse(null, 'Candidate deleted successfully');
        } catch (Throwable $e) {
            Log::error('Failed to delete candidate via API', [
                'candidate_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse($e->getMessage() ?: 'Candidate data could not be deleted.', 422);
        }
    }

    public function promoteToEmployee(int $id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'department_id' => ['nullable', 'integer'],
            'join_date' => ['nullable', 'date'],
        ]);

        try {
            $candidate = $this->hrdService->findWithDetails($id);

            if ((int) $candidate->status !== Candidate::STATUS_ACCEPTED) {
                return $this->errorResponse('Candidate must be accepted before being promoted to employee.', 422);
            }

            $employee = $this->hrdService->promoteCandidateToEmployee($candidate, $validated);

            return $this->successResponse(
                [
                    'candidate_id' => $candidate->id,
                    'employee_id' => $employee->id,
                    'employee' => $employee,
                ],
                'Candidate promoted to employee successfully',
                201
            );
        } catch (Throwable $e) {
            Log::error('Failed to promote candidate to employee via API', [
                'candidate_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse($e->getMessage() ?: 'Candidate could not be promoted to employee.', 500);
        }
    }
}
