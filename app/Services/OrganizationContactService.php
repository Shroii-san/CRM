<?php

namespace App\Services;

use App\Models\OrganizationContact;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

class OrganizationContactService
{
    public function __construct(private PersonService $personService)
    {
    }

    /**
     * Mendapatkan daftar kontak organisasi (PIC).
     */
    public function list(?int $organizationId = null, ?string $search = null, int $perPage = 50, int $page = 1)
    {
        $query = OrganizationContact::query()
            ->with(['organization', 'person'])
            ->orderBy('created_at', 'desc');

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('job_title', 'like', "%{$search}%")
                    ->orWhereHas('person', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Membuat kontak organisasi baru.
     */
    public function create(array $data): OrganizationContact
    {
        return DB::transaction(function () use ($data) {
            $person = $this->personService->findOrCreate([
                'name'  => $data['name'] ?? ($data['pic_name'] ?? 'Contact Person'),
                'email' => $data['email'] ?? ($data['pic_email'] ?? null),
                'phone' => $data['phone'] ?? ($data['pic_phone'] ?? null),
            ]);

            return OrganizationContact::create([
                'organization_id' => $data['organization_id'] ?? $data['company_id'],
                'person_id'       => $person->id,
                'job_title'       => $data['job_title'] ?? ($data['position'] ?? null),
                'is_primary'      => $data['is_primary'] ?? true,
                'started_at'      => $data['started_at'] ?? now(),
                'ended_at'        => $data['ended_at'] ?? null,
            ]);
        });
    }

    /**
     * Memperbarui data kontak organisasi.
     */
    public function update(int $id, array $data): OrganizationContact
    {
        return DB::transaction(function () use ($id, $data) {
            $contact = OrganizationContact::findOrFail($id);

            if ($contact->person) {
                $contact->person->update(array_filter([
                    'name'  => $data['name'] ?? ($data['pic_name'] ?? null),
                    'email' => $data['email'] ?? ($data['pic_email'] ?? null),
                    'phone' => $data['phone'] ?? ($data['pic_phone'] ?? null),
                ]));
            }

            $contact->update(array_filter([
                'job_title'  => $data['job_title'] ?? ($data['position'] ?? null),
                'is_primary' => isset($data['is_primary']) ? (bool) $data['is_primary'] : null,
            ]));

            return $contact->refresh();
        });
    }

    /**
     * Menghapus kontak organisasi.
     */
    public function delete(int $id): void
    {
        $contact = OrganizationContact::findOrFail($id);
        $contact->delete();
    }
}
