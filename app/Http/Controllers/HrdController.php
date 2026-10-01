<?php

namespace App\Http\Controllers;

use App\Models\Kandidat;
use App\Models\Karyawan;
use App\Models\Keluarga;
use App\Models\RiwayatPendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HrdController extends Controller
{
    /* ---------------- INDEX ---------------- */
    public function index()
    {
        $kandidat = Kandidat::with(['pendidikan', 'karyawan', 'keluarga'])->latest()->get();
        return view('hrd.index', compact('kandidat'));
    }

    /* ---------------- RULES ---------------- */
    private function rules(bool $isUpdate = false): array
    {
        return [
            'nama_lengkap' => 'required|string|max:255',
            'nik'          => 'required|digits:16',
            'no_telepon'   => 'required|string|max:15',
            'divisi'       => 'required|string|max:100',
            'tgl_lamar'    => 'required|date',

            'npwp'    => 'nullable|string|max:50',
            'no_bpjs' => 'nullable|string|max:50',

            'status' => $isUpdate
                ? 'required|in:pending,diterima,ditolak'
                : 'nullable|in:pending,diterima,ditolak',

            'foto'     => 'nullable|image|max:2048',
            'scan_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'cv'       => 'nullable|file|mimes:pdf,doc,docx|max:4096',

            'pendidikan'                => 'nullable|array',
            'pendidikan.*.jenjang'      => 'required|string|max:20',
            'pendidikan.*.nama_sekolah' => 'required|string|max:255',
            'pendidikan.*.jurusan'      => 'nullable|string|max:255',
            'pendidikan.*.gelar'        => 'nullable|string|max:100',
            'pendidikan.*.ipk_atau_nilai' => 'nullable|string|max:15',
            'pendidikan.*.lokasi'       => 'nullable|string|max:255',
            'pendidikan.*.tahun_masuk'  => 'required|integer|min:1900|max:2100',
            'pendidikan.*.tahun_lulus'  => 'nullable|integer|min:1900|max:2100',

            'keluarga'                  => 'nullable|array',
            'keluarga.*.id'             => 'nullable|integer|exists:keluarga,id',
            'keluarga.*.nama'           => 'required|string|max:255',
            'keluarga.*.tempat_lahir'   => 'required|string|max:255',
            'keluarga.*.tanggal_lahir'  => 'required|date|before_or_equal:today',
            'keluarga.*.status'         => 'required|in:Orang Tua,Ayah,Ibu,Suami,Istri,Anak',
        ];
    }

    /* ---------------- BERSIHKAN BARIS PENDIDIKAN KOSONG ---------------- */
    private function bersihkanPendidikan(Request $request): void
    {
        $rows = collect($request->input('pendidikan', []))
            ->filter(fn ($r) => is_array($r) && collect($r)->contains(fn ($v) => filled($v)))
            ->values()
            ->all();

        $request->merge(['pendidikan' => $rows]);
    }

    private function bersihkanKeluarga(Request $request): void
    {
        $rows = collect($request->input('keluarga', []))
            ->filter(fn ($r) => is_array($r) && collect($r)->contains(fn ($v) => filled($v)))
            ->values()->all();

        foreach ($rows as &$row) {
            if (!empty($row['tanggal_lahir'])) {
                $value = trim($row['tanggal_lahir']);
                $date = \DateTime::createFromFormat('d/m/Y', $value);
                if ($date && $date->format('d/m/Y') === $value) {
                    $row['tanggal_lahir'] = $date->format('Y-m-d');
                }
            }
        }
        unset($row);

        $request->merge(['keluarga' => $rows]);
    }

    /* ---------------- STORE ---------------- */
    public function store(Request $request)
    {
        if ($request->file('foto')?->getError() === UPLOAD_ERR_INI_SIZE) {
            return back()->withInput()->with('error', 'Foto gagal diupload karena ukurannya melewati batas upload PHP (2 MB).');
        }

        $this->bersihkanPendidikan($request);
        $this->bersihkanKeluarga($request);

        $data       = $request->validate($this->rules(false));
        $pendidikan = $data['pendidikan'] ?? [];
        $keluarga = $data['keluarga'] ?? [];
        unset($data['pendidikan']);
        unset($data['keluarga']);

        $data['status'] = 'pending';   // kandidat baru selalu pending

        $newFiles = [];

        try {
            DB::transaction(function () use ($request, &$data, $pendidikan, $keluarga, &$newFiles) {
                $folders = [
                    'foto'     => 'kandidat/foto',
                    'scan_ktp' => 'kandidat/ktp',
                    'cv'       => 'kandidat/cv',
                ];

                foreach ($folders as $field => $dir) {
                    if ($request->hasFile($field)) {
                        $data[$field] = $request->file($field)->store($dir, 'public');
                        $newFiles[]   = $data[$field];
                    }
                }

                $kandidat = Kandidat::create($data);

                if (!empty($pendidikan)) {
                    $kandidat->pendidikan()->createMany($pendidikan);
                }
                if (!empty($keluarga)) {
                    $kandidat->keluarga()->createMany($keluarga);
                }
            });
        } catch (Throwable $e) {
            foreach ($newFiles as $path) Storage::disk('public')->delete($path);
            report($e);

            return back()->withInput()->with('error', 'Data kandidat gagal disimpan. Periksa kembali data yang diisi.');
        }

        return redirect()->route('hrd.index')->with('success', 'Calon karyawan berhasil ditambahkan!');
    }

    /* ---------------- UPDATE ---------------- */
    public function update(Request $request, $id)
    {
        $kandidat = Kandidat::findOrFail($id);

        if ($request->file('foto')?->getError() === UPLOAD_ERR_INI_SIZE) {
            return back()->withInput()->with('error', 'Foto gagal diupload karena ukurannya melewati batas upload PHP (2 MB).');
        }

        // Simpan dulu apakah form beneran ngirim field pendidikan
        $pendidikanDikirim = $request->has('pendidikan');

        $this->bersihkanPendidikan($request);
        $this->bersihkanKeluarga($request);

        $data       = $request->validate($this->rules(true));
        $pendidikan = $data['pendidikan'] ?? [];
        $keluarga = $data['keluarga'] ?? [];
        unset($data['pendidikan']);
        unset($data['keluarga']);

        $oldFiles = [];
        $newFiles = [];

        try {
            DB::transaction(function () use (
                $request, $kandidat, &$data, $pendidikan, $keluarga, $pendidikanDikirim, &$oldFiles, &$newFiles
            ) {
                $folders = [
                    'foto'     => 'kandidat/foto',
                    'scan_ktp' => 'kandidat/ktp',
                    'cv'       => 'kandidat/cv',
                ];

                foreach ($folders as $field => $dir) {
                    if ($request->hasFile($field)) {
                        if (!empty($kandidat->{$field})) $oldFiles[] = $kandidat->{$field};

                        $data[$field] = $request->file($field)->store($dir, 'public');
                        $newFiles[]   = $data[$field];
                    }
                }

                $kandidat->update($data);

                if ($pendidikanDikirim) {
                    $kandidat->pendidikan()->delete();

                    if (!empty($pendidikan)) {
                        $kandidat->pendidikan()->createMany($pendidikan);
                    }
                }

                if ($request->has('keluarga_submitted')) {
                    $familyIds = collect($keluarga)->pluck('id')->filter()->all();
                    $kandidat->keluarga()->when(
                        !empty($familyIds),
                        fn ($query) => $query->whereNotIn('id', $familyIds),
                        fn ($query) => $query
                    )->delete();

                    foreach ($keluarga as $familyData) {
                        $familyId = $familyData['id'] ?? null;
                        unset($familyData['id']);

                        if ($familyId) {
                            $kandidat->keluarga()->whereKey($familyId)->firstOrFail()->update($familyData);
                        } else {
                            $kandidat->keluarga()->create($familyData);
                        }
                    }
                }
            });
        } catch (Throwable $e) {
            foreach ($newFiles as $path) Storage::disk('public')->delete($path);
            throw $e;
        }

        // Hapus file lama setelah transaksi sukses
        foreach ($oldFiles as $path) Storage::disk('public')->delete($path);

        if (($data['status'] ?? null) === 'diterima') {
            $this->buatKaryawanJikaBelumAda($kandidat, $request->input('tanggal_masuk'));
        }

        return redirect()->route('hrd.index')->with('success', 'Data kandidat berhasil diupdate!');
    }

    /* ---------------- DESTROY ---------------- */
    public function destroy($id)
    {
        $kandidat = Kandidat::findOrFail($id);

        $files = array_filter([$kandidat->foto, $kandidat->scan_ktp, $kandidat->cv]);

        DB::transaction(function () use ($kandidat) {
            $kandidat->pendidikan()->delete();
            $kandidat->keluarga()->delete();
            $kandidat->delete();
        });

        foreach ($files as $path) Storage::disk('public')->delete($path);

        return redirect()->route('hrd.index')->with('success', 'Data berhasil dihapus!');
    }

    /* ---------------- JADIKAN KARYAWAN ---------------- */
    public function jadikanKaryawan(Request $request, $id)
    {
        $input = $request->validate([
            'departemen_id' => 'nullable|integer',
            'tanggal_masuk' => 'nullable|date',
        ]);

        $kandidat = Kandidat::with(['pendidikan', 'keluarga'])->findOrFail($id);

        if ($kandidat->status !== 'diterima') {
            return back()->with('error', 'Kandidat harus berstatus "Diterima" dulu.');
        }

        // kandidat_id di tabel karyawans unique -> cuma boleh sekali
        if ($kandidat->karyawan()->exists()) {
            return back()->with('error', 'Kandidat ini sudah jadi karyawan.');
        }

        $this->buatKaryawanJikaBelumAda($kandidat, $input['tanggal_masuk'] ?? null, $input['departemen_id'] ?? null);

        return redirect()->route('karyawan.index')
            ->with('success', 'Kandidat berhasil dijadikan karyawan.');
    }

    private function buatKaryawanJikaBelumAda(Kandidat $kandidat, ?string $tanggalMasuk = null, ?int $departemenId = null): void
    {
        if ($kandidat->karyawan()->exists()) {
            return;
        }

        $kandidat->loadMissing(['pendidikan', 'keluarga']);

        DB::transaction(function () use ($kandidat, $tanggalMasuk, $departemenId) {
            $karyawan = $kandidat->karyawan()->create([
                'departemen_id' => $departemenId,
                'nama_lengkap'  => $kandidat->nama_lengkap,
                'nik'           => $kandidat->nik,
                'no_telepon'    => $kandidat->no_telepon,
                'tanggal_masuk' => $tanggalMasuk ?? now()->toDateString(),
                'status_kerja'  => 'aktif',
                'foto'          => $kandidat->foto,
                'scan_ktp'      => $kandidat->scan_ktp,
                'cv'            => $kandidat->cv,
                'npwp'          => $kandidat->npwp,
                'no_bpjs'        => $kandidat->no_bpjs,
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
    }
}
