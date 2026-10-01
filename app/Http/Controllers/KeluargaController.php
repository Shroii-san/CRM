<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Keluarga;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KeluargaController extends Controller
{   

    public function store(Request $request, Karyawan $karyawan)
    {
        $keluarga = $karyawan->keluarga()->create($this->validatedData($request));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data keluarga berhasil ditambahkan.',
                'data' => $this->familyResponse($keluarga),
            ], 201);
        }

        return back()->with('success', 'Data keluarga berhasil ditambahkan.');
    }

    public function update(Request $request, Keluarga $keluarga)
    {
        $keluarga->update($this->validatedData($request));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data keluarga berhasil diperbarui.',
                'data' => $this->familyResponse($keluarga->fresh()),
            ]);
        }

        return back()->with('success', 'Data keluarga berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id)
    {
        $keluarga = Keluarga::find($id);

        if (!$keluarga) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Data keluarga sudah tidak tersedia.',
                    'deleted_id' => $id,
                ]);
            }

            return back()->with('success', 'Data keluarga sudah tidak tersedia.');
        }

        $keluarga->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data keluarga berhasil dihapus.',
                'deleted_id' => $id,
            ]);
        }

        return back()->with('success', 'Data keluarga berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $request->merge([
            'tanggal_lahir' => $this->normalizeDate($request->input('tanggal_lahir')),
        ]);

        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['required', 'in:Orang Tua,Ayah,Ibu,Suami,Istri,Anak'],
        ]);
    }

    private function familyResponse(Keluarga $keluarga): array
    {
        return [
            'id' => $keluarga->id,
            'nama' => $keluarga->nama,
            'tempat_lahir' => $keluarga->tempat_lahir,
            'tanggal_lahir' => $keluarga->tanggal_lahir?->format('Y-m-d'),
            'status' => $keluarga->status,
        ];
    }

    private function normalizeDate(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim($value);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        try {
            $date = Carbon::createFromFormat('d/m/Y', $value);
            if (!$date || $date->format('d/m/Y') !== $value) {
                return $value;
            }

            return $date->format('Y-m-d');
        } catch (\Throwable $e) {
            return $value;
        }
    }
}
