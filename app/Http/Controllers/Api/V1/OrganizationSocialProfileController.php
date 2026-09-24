<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationSocialProfile\StoreOrganizationSocialProfileRequest;
use App\Http\Requests\OrganizationSocialProfile\UpdateOrganizationSocialProfileRequest;
use App\Http\Resources\OrganizationSocialProfile\OrganizationSocialProfileResource;
use App\Services\OrganizationSocialProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationSocialProfileController extends Controller
{
    public function __construct(private OrganizationSocialProfileService $profileService)
    {
    }

    /**
     * Mendapatkan daftar profil sosial media organisasi.
     */
    public function index(Request $request): JsonResponse
    {
        $organizationId = $request->input('organization_id') ? (int) $request->input('organization_id') : null;
        $profiles = $this->profileService->list($organizationId);

        return response()->json([
            'data' => OrganizationSocialProfileResource::collection($profiles)
        ]);
    }

    /**
     * Membuat data profil sosial media organisasi baru.
     */
    public function store(StoreOrganizationSocialProfileRequest $request): JsonResponse
    {
        $profile = $this->profileService->create($request->validated());

        return response()->json([
            'message' => 'Organization social profile created successfully',
            'data' => OrganizationSocialProfileResource::make($profile)
        ], 201);
    }

    /**
     * Memperbarui data profil sosial media organisasi.
     */
    public function update(UpdateOrganizationSocialProfileRequest $request, int|string $id): JsonResponse
    {
        $profile = $this->profileService->update((int) $id, $request->validated());

        return response()->json([
            'message' => 'Organization social profile updated successfully',
            'data' => OrganizationSocialProfileResource::make($profile)
        ]);
    }

    /**
     * Menghapus data profil sosial media organisasi.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->profileService->delete((int) $id);

        return response()->json([
            'message' => 'Organization social profile deleted successfully'
        ]);
    }
}
