<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;

class ClientExportService
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    /**
     * Export clients as CSV if the user can view the client menu.
     */
    public function exportCsv(User $user)
    {
        if (!$this->permissionService->hasPermission($user, 'clients', 'view')) {
            abort(403, 'Anda tidak memiliki akses untuk export client.');
        }

        $query = Client::query()->with(['person', 'organization', 'source']);

        $filename = 'clients-' . date('Y-m-d-His') . '.csv';

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'id',
                'person_id',
                'organization_id',
                'type',
                'name',
                'email',
                'phone',
                'source',
                'source_id',
                'is_active',
            ]);

            foreach ($query->get() as $client) {
                $type = $client->organization_id ? 'organization' : ($client->person_id ? 'person' : 'unknown');
                $name = $client->organization?->name ?? $client->person?->name ?? '-';
                $email = $client->organization?->email ?? $client->person?->email ?? '-';
                $phone = $client->organization?->phone ?? $client->person?->phone ?? '-';

                fputcsv($handle, [
                    $client->id,
                    $client->person_id,
                    $client->organization_id,
                    $type,
                    $name,
                    $email,
                    $phone,
                    $client->source?->name ?? '-',
                    $client->source_id,
                    $client->is_active ? 'active' : 'inactive',
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
