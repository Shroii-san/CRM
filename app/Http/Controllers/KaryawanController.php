<?php

namespace App\Http\Controllers;

use App\Models\Kandidat;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    public function index()
    {
        $this->syncAcceptedCandidates();
        $karyawan = Karyawan::with(['pendidikan', 'keluarga'])->latest()->get();
        return view('karyawan', compact('karyawan'));
    }

    private function syncAcceptedCandidates(): void
    {
        Kandidat::with(['pendidikan', 'keluarga'])
            ->where('status', 'diterima')
            ->whereDoesntHave('karyawan')
            ->get()
            ->each(function (Kandidat $kandidat): void {
                DB::transaction(function () use ($kandidat): void {
                    $karyawan = $kandidat->karyawan()->create([
                        'departemen_id' => null,
                        'nama_lengkap' => $kandidat->nama_lengkap,
                        'nik' => $kandidat->nik,
                        'no_telepon' => $kandidat->no_telepon,
                        'tanggal_masuk' => now()->toDateString(),
                        'status_kerja' => 'aktif',
                        'foto' => $kandidat->foto,
                        'scan_ktp' => $kandidat->scan_ktp,
                        'cv' => $kandidat->cv,
                        'npwp' => $kandidat->npwp,
                        'no_bpjs' => $kandidat->no_bpjs,
                    ]);

                    foreach ($kandidat->pendidikan as $pendidikan) {
                        $karyawan->pendidikan()->create($pendidikan->only([
                            'jenjang', 'nama_sekolah', 'jurusan', 'gelar',
                            'ipk_atau_nilai', 'lokasi', 'tahun_masuk', 'tahun_lulus',
                        ]));
                    }

                    foreach ($kandidat->keluarga as $keluarga) {
                        $karyawan->keluarga()->create($keluarga->only([
                            'nama', 'tempat_lahir', 'tanggal_lahir', 'status',
                        ]));
                    }
                });
            });
    }
}
