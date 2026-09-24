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
            $personId = $data['person_id'] ?? null;

            if (!$personId) {
                $person = $this->personService->findOrCreate([
                    'name'  => $data['name'],
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,
                ]);
                $personId = $person->id;
            }

            return OrganizationContact::create([
                'organization_id' => $data['organization_id'],
                'person_id'       => $personId,
                'job_title'       => $data['job_title'] ?? null,
                'is_primary'      => $data['is_primary'] ?? true,
                'started_at'      => $data['started_at'] ?? now()->toDateString(),
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

            // Update data person jika ada input name/email/phone
            if ($contact->person) {
                $personUpdate = [];
                if (array_key_exists('name', $data)) $personUpdate['name'] = $data['name'];
                if (array_key_exists('email', $data)) $personUpdate['email'] = $data['email'];
                if (array_key_exists('phone', $data)) $personUpdate['phone'] = $data['phone'];

                if (!empty($personUpdate)) {
                    $contact->person->update($personUpdate);
                }
            }

            // Update data organization_contact (memungkinkan set ke null)
            $contactUpdate = [];
            if (array_key_exists('job_title', $data)) $contactUpdate['job_title'] = $data['job_title'];
            if (array_key_exists('is_primary', $data)) $contactUpdate['is_primary'] = (bool) $data['is_primary'];
            if (array_key_exists('started_at', $data)) $contactUpdate['started_at'] = $data['started_at'];
            if (array_key_exists('ended_at', $data)) $contactUpdate['ended_at'] = $data['ended_at'];

            if (!empty($contactUpdate)) {
                $contact->update($contactUpdate);
            }

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
