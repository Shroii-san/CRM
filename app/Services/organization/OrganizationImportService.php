<?php

namespace App\Services\organization;

use App\Models\{Organization, OrganizationContact, OrganizationSocialProfiles, District, Industry, Person, Province, Regency, Village};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class OrganizationImportService
{
    private array $regionLookupCache = [];

    private const HEADER_ALIASES = [
        // Name
        'namaperusahaan'   => 'name',
        'namaorganisasi'   => 'name',
        'organizationname' => 'name',
        'rumahsakit'       => 'name',
        'nama'             => 'name',
        'name'             => 'name',

        // Code (diabaikan atau dinormalkan, ID digenerate otomatis)
        'kodeorganisasi'   => 'organization_code',
        'kode'             => 'organization_code',
        'organizationcode' => 'organization_code',

        // Industry
        'jenis'            => 'industry',
        'organizationtype' => 'industry',
        'industri'         => 'industry',
        'industry'         => 'industry',
        'tipe'             => 'industry',

        // Tier / Kelas
        'tier'             => 'tier',
        'kelas'            => 'tier',
        'level'            => 'tier',

        // Regions
        'provinsi'         => 'province',
        'province'         => 'province',
        'kabkota'          => 'regency',
        'kabupaten'        => 'regency',
        'kabupatenkota'    => 'regency',
        'regency'          => 'regency',
        'city'             => 'regency',
        'kota'             => 'regency',
        'kecamatan'        => 'district',
        'district'         => 'district',
        'kelurahan'        => 'village',
        'desa'             => 'village',
        'village'          => 'village',

        // Contacts & General Info
        'alamat'           => 'address',
        'address'          => 'address',
        'telepon'          => 'phone',
        'telp'             => 'phone',
        'phone'            => 'phone',
        'email'            => 'email',
        'website'          => 'website',
        'web'              => 'website',
        'deskripsi'        => 'description',
        'description'      => 'description',
        'keterangan'       => 'description',

        // Status & Active
        'status'           => 'status',
        'aktif'            => 'is_active',
        'isactive'         => 'is_active',
        'is_active'        => 'is_active',

        // External IDs / Specific fields
        'nosp'             => 'sp_number',
        'nomorsp'          => 'sp_number',
        'spnumber'         => 'sp_number',
        'linkkemkes'       => 'kemkes_link',
        'linkdetailkemkes' => 'kemkes_link',
        'kemkeslink'       => 'kemkes_link',

        // Coordinates
        'lat'              => 'latitude',
        'latitude'         => 'latitude',
        'long'             => 'longitude',
        'longitude'        => 'longitude',

        // PIC / Contact Person
        'pic'              => 'pic',
        'picname'          => 'pic',
        'namapic'          => 'pic',
        'picposition'      => 'pic_position',
        'jabatanpic'       => 'pic_position',
        'posisipic'        => 'pic_position',
        'jobtitle'         => 'pic_position',
        'picphone'         => 'pic_phone',
        'teleponpic'       => 'pic_phone',
        'telppic'          => 'pic_phone',
        'picemail'         => 'pic_email',
        'emailpic'         => 'pic_email',

        // Social profiles
        'linkedin'         => 'linkedin',
        'instagram'        => 'instagram',
        'facebook'         => 'facebook',
        'twitter'          => 'twitter',
    ];

    /**
     * Memproses file import Excel/CSV ke database organisasi.
     *
     * @param UploadedFile $file Berkas spreadsheet yang diunggah
     * @param mixed $user Model User atau null
     * @param string|int|null $defaultIndustryId ID industri default bila kolom industri di baris kosong
     * @return array
     */
    public function import(UploadedFile $file, mixed $user = null, mixed $defaultIndustryId = null): array
    {
        // Defensive parameter handling jika dipanggil import($file, $industryId)
        if (is_string($user) || is_numeric($user)) {
            $defaultIndustryId = (string) $user;
            $user = null;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['csv', 'xlsx', 'xls'], true)) {
            throw new \InvalidArgumentException('File harus berformat CSV, XLS, atau XLSX.');
        }

        $path = $file->getRealPath();
        $rows = IOFactory::createReaderForFile($path)
            ->load($path)
            ->getActiveSheet()
            ->toArray(null, true, true, false);

        if (empty($rows)) {
            throw new \InvalidArgumentException('File kosong atau tidak memiliki data.');
        }

        $rawHeader = array_shift($rows);
        $header = array_map(
            fn($v) => self::HEADER_ALIASES[$k = preg_replace('/[^a-z0-9]/', '', strtolower(trim((string) $v)))] ?? $k,
            $rawHeader
        );

        if (!in_array('name', $header, true)) {
            throw new \InvalidArgumentException('Kolom nama organisasi (Nama Organisasi / Nama Perusahaan / Rumah Sakit) tidak ditemukan pada baris judul.');
        }

        $defaultIndustry = $defaultIndustryId
            ? Industry::find($defaultIndustryId, ['id', 'name'])
            : null;

        return DB::transaction(function () use ($rows, $header, $defaultIndustry) {
            $result = ['imported' => 0, 'created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

            foreach ($rows as $index => $row) {
                if (!array_filter($row, fn($v) => $v !== null && trim((string) $v) !== '')) {
                    continue;
                }

                $row = array_pad($row, count($header), null);
                $data = array_combine($header, array_slice($row, 0, count($header)));

                try {
                    $status = $this->importRow($data, $defaultIndustry);
                    $result[$status]++;
                    $result['imported']++;
                } catch (\Throwable $e) {
                    $result['errors'][] = 'Baris ' . ($index + 2) . ': ' . $e->getMessage();
                }
            }

            return $result;
        });
    }

    /**
     * Mengimpor satu baris data organisasi.
     *
     * @return 'created'|'updated'|'skipped'
     */
    private function importRow(array $data, ?Industry $defaultIndustry): string
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            throw new \RuntimeException('Nama organisasi tidak boleh kosong');
        }

        // Resolusi Tier
        $tier = strtoupper(trim((string) ($data['tier'] ?? '')));
        if ($tier === '') {
            $tier = 'A';
        } elseif (!in_array($tier, ['A', 'B', 'C', 'D'], true)) {
            throw new \RuntimeException("Tier harus A, B, C, atau D (ditemukan: '{$tier}')");
        }

        // Resolusi Status Aktif (boolean is_active)
        $rawStatus = trim((string) ($data['is_active'] ?? $data['status'] ?? ''));
        if ($rawStatus !== '') {
            $rawStatusLower = strtolower($rawStatus);
            if (in_array($rawStatusLower, ['inactive', 'nonaktif', 'tidak', 'no', '0', 'false', 'f'], true)) {
                $isActive = false;
            } else {
                $isActive = true;
            }
        } else {
            $isActive = true;
        }

        // Resolusi Industri
        $industryName = trim((string) ($data['industry'] ?? ''));
        $industry = null;
        if ($industryName !== '') {
            $industry = Industry::where('name', $industryName)
                ->orWhere('id', is_numeric($industryName) ? (int) $industryName : 0)
                ->first(['id', 'name']);
        }
        if (!$industry && $defaultIndustry) {
            $industry = $defaultIndustry;
        }
        if (!$industry) {
            throw new \RuntimeException("Industri tidak valid atau belum ditentukan untuk organisasi '{$name}'");
        }

        // Resolusi Relasi Wilayah Administratif
        $provinceId = $this->findRegionId(Province::class, $data['province'] ?? null);
        $regencyId  = $this->findRegionId(Regency::class, $data['regency'] ?? null, 'province_id', $provinceId);
        $districtId = $this->findRegionId(District::class, $data['district'] ?? null, 'regency_id', $regencyId);
        $villageId  = $this->findRegionId(Village::class, $data['village'] ?? null, 'district_id', $districtId);

        $kemkesLink = trim((string) ($data['kemkes_link'] ?? '')) ?: null;

        // Pencarian Organisasi Eksisting (berdasarkan kemkes_link atau name)
        $organization = null;
        if ($kemkesLink) {
            $organization = Organization::where('kemkes_link', $kemkesLink)->orderBy('id')->first();
        }

        if (!$organization) {
            $query = Organization::where('name', $name);
            if ($provinceId) {
                $query->where('province_id', $provinceId);
            }
            if ($regencyId) {
                $query->where('regency_id', $regencyId);
            }
            $organization = $query->orderBy('id')->first();
        }

        // Data yang disimpan murni sesuai kolom fisik tabel `organizations` baru
        $organizationData = [
            'name'        => $name,
            'industry_id' => $industry->id,
            'tier'        => $tier,
            'description' => trim((string) ($data['description'] ?? '')) ?: null,
            'is_active'   => $isActive,
            'address'     => trim((string) ($data['address'] ?? '')) ?: null,
            'province_id' => $provinceId,
            'regency_id'  => $regencyId,
            'district_id' => $districtId,
            'village_id'  => $villageId,
            'phone'       => trim((string) ($data['phone'] ?? '')) ?: null,
            'email'       => trim((string) ($data['email'] ?? '')) ?: null,
            'website'     => trim((string) ($data['website'] ?? '')) ?: null,
            'kemkes_link' => $kemkesLink,
            'sp_number'   => trim((string) ($data['sp_number'] ?? '')) ?: null,
            'latitude'    => $this->normalizeCoordinate($data['latitude'] ?? null, 90),
            'longitude'   => $this->normalizeCoordinate($data['longitude'] ?? null, 180),
        ];

        if ($organization) {
            $fillEmpty = [];
            foreach ($organizationData as $field => $value) {
                if (($organization->{$field} === null || $organization->{$field} === '') && $value !== null && $value !== '') {
                    $fillEmpty[$field] = $value;
                }
            }

            if ($fillEmpty !== []) {
                $organization->update($fillEmpty);
                $result = 'updated';
            } else {
                $result = 'skipped';
            }
        } else {
            $organization = Organization::create($organizationData);
            $result = 'created';
        }

        // Simpan / Kaitkan PIC Person
        $picName = trim((string) ($data['pic'] ?? ''));
        if ($picName !== '') {
            $picEmail = trim((string) ($data['pic_email'] ?? '')) ?: null;
            $picPhone = trim((string) ($data['pic_phone'] ?? '')) ?: null;

            $personQuery = Person::where('name', $picName);
            if ($picEmail) {
                $personQuery->where(fn($q) => $q->where('email', $picEmail)->orWhereNull('email'));
            }
            $person = $personQuery->first();

            if (!$person) {
                $person = Person::create([
                    'name'  => $picName,
                    'email' => $picEmail,
                    'phone' => $picPhone,
                ]);
            }

            OrganizationContact::firstOrCreate(
                [
                    'organization_id' => $organization->getKey(),
                    'person_id'       => $person->getKey(),
                ],
                [
                    'job_title'  => trim((string) ($data['pic_position'] ?? '')) ?: null,
                    'is_primary' => true,
                ]
            );
        }

        // Simpan Profil Sosial Media bila ada
        foreach (['linkedin', 'instagram', 'facebook', 'twitter'] as $platform) {
            $socialValue = trim((string) ($data[$platform] ?? ''));
            if ($socialValue !== '') {
                $isUrl = str_starts_with($socialValue, 'http://') || str_starts_with($socialValue, 'https://');
                OrganizationSocialProfiles::firstOrCreate(
                    [
                        'organization_id' => $organization->getKey(),
                        'platform'        => $platform,
                    ],
                    [
                        'username' => $isUrl ? (basename(parse_url($socialValue, PHP_URL_PATH) ?? $socialValue) ?: $socialValue) : $socialValue,
                        'url'      => $isUrl ? $socialValue : null,
                    ]
                );
            }
        }

        return $result;
    }

    private function findRegionId(
        string $model,
        mixed $value,
        ?string $parentColumn = null,
        mixed $parentId = null
    ): ?string {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $cacheKey = implode('|', [
            $model,
            (string) $parentColumn,
            (string) $parentId,
            $value,
        ]);

        if (array_key_exists($cacheKey, $this->regionLookupCache)) {
            return $this->regionLookupCache[$cacheKey];
        }

        $result = $model::query()
            ->when($parentColumn && $parentId, fn($q) => $q->where($parentColumn, $parentId))
            ->where(fn($q) => $q->where('id', $value)->orWhere('name', $value))
            ->value('id');

        $this->regionLookupCache[$cacheKey] = $result;

        return $result;
    }

    private function normalizeCoordinate(mixed $value, float $limit = 180): ?float
    {
        $value = str_replace(',', '.', trim((string) $value));

        if (!is_numeric($value)) {
            return null;
        }

        $number = (float) $value;

        return abs($number) <= $limit ? $number : null;
    }
}
