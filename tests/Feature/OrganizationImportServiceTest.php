<?php

namespace Tests\Feature;

use App\Models\{District, Industry, Organization, OrganizationContact, Person, Province, Regency, Village};
use App\Services\organization\OrganizationImportService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OrganizationImportServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('organization_social_profiles');
        Schema::dropIfExists('organization_contacts');
        Schema::dropIfExists('persons');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('industries');
        Schema::dropIfExists('villages');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');

        Schema::create('provinces', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->string('country_id', 20)->nullable();
            $table->string('name', 100);
        });

        Schema::create('regencies', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->string('province_id', 20);
            $table->string('name', 100);
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->string('regency_id', 20);
            $table->string('name', 100);
        });

        Schema::create('villages', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->string('district_id', 20);
            $table->string('name', 100);
        });

        Schema::create('industries', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100)->unique();
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
        });

        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('industry_id');
            $table->char('tier', 1)->default('A');
            $table->string('name', 255)->unique();
            $table->string('email', 255)->nullable()->unique();
            $table->string('phone', 20)->nullable()->unique();
            $table->string('website', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('sp_number', 50)->nullable();
            $table->string('kemkes_link', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('province_id', 20)->nullable();
            $table->string('regency_id', 20)->nullable();
            $table->string('district_id', 20)->nullable();
            $table->string('village_id', 20)->nullable();
            $table->timestampsTz();
        });

        Schema::create('persons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->timestampsTz();
        });

        Schema::create('organization_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('organization_id');
            $table->unsignedInteger('person_id');
            $table->string('job_title', 100)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestampsTz();
        });

        Province::query()->insert(['id' => '32', 'country_id' => 'ID', 'name' => 'Jawa Barat']);
        Regency::query()->insert(['id' => '3201', 'province_id' => '32', 'name' => 'Bogor']);
        District::query()->insert(['id' => '320101', 'regency_id' => '3201', 'name' => 'Bogor Selatan']);
        Village::query()->insert(['id' => '3201011001', 'district_id' => '320101', 'name' => 'Bojong Jaya']);

        Industry::create(['name' => 'Rumah Sakit', 'is_active' => true]);
    }

    public function test_it_reuses_cached_region_lookup_for_repeated_values_within_the_same_service_instance(): void
    {
        $service = new OrganizationImportService();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $provinceId = $this->callPrivateMethod($service, 'findRegionId', [Province::class, 'Jawa Barat']);
        $sameProvinceId = $this->callPrivateMethod($service, 'findRegionId', [Province::class, 'Jawa Barat']);

        $this->assertSame('32', $provinceId);
        $this->assertSame('32', $sameProvinceId);
        $this->assertSame(1, count(DB::getQueryLog()));
    }

    public function test_it_successfully_imports_csv_with_exported_headers_and_ignores_organization_code(): void
    {
        $service = new OrganizationImportService();

        $csvContent = implode("\n", [
            'Nama Organisasi,Kode Organisasi,Industri,Tier,Deskripsi,Status,Alamat,Provinsi,Kabupaten/Kota,Kecamatan,Kelurahan,Telepon,Email,Website,Nomor SP,Link Kemkes,Latitude,Longitude,Aktif,PIC,Jabatan PIC,Telepon PIC,Email PIC',
            'RSUD Cibinong,00099,Rumah Sakit,B,RSUD Rujukan,Active,Jl. Raya Jakarta-Bogor,Jawa Barat,Bogor,Bogor Selatan,Bojong Jaya,0218765432,info@rsudcibinong.id,https://rsudcibinong.id,SP-001,https://kemkes.go.id/rs/99,-6.48,106.85,Ya,Dr. Ahmad,Direktur,08123456789,ahmad@rsudcibinong.id',
        ]);

        $tmpFile = tempnam(sys_get_temp_dir(), 'test_import_') . '.csv';
        file_put_contents($tmpFile, $csvContent);

        $uploadedFile = new UploadedFile($tmpFile, 'test.csv', 'text/csv', null, true);

        $result = $service->import($uploadedFile);

        $this->assertSame(1, $result['imported']);
        $this->assertSame(1, $result['created']);
        $this->assertSame(0, $result['errors'] ? count($result['errors']) : 0);

        $org = Organization::where('name', 'RSUD Cibinong')->first();
        $this->assertNotNull($org);
        $this->assertSame('B', $org->tier);
        $this->assertTrue($org->is_active);
        $this->assertSame('32', $org->province_id);
        $this->assertSame('3201', $org->regency_id);
        $this->assertSame('320101', $org->district_id);
        $this->assertSame('3201011001', $org->village_id);

        // Verifikasi bahwa accessor organization_code otomatis tersedia
        $this->assertSame(str_pad((string) $org->id, 5, '0', STR_PAD_LEFT), $org->organization_code);

        // Verifikasi PIC terkait
        $contact = OrganizationContact::where('organization_id', $org->id)->with('person')->first();
        $this->assertNotNull($contact);
        $this->assertSame('Direktur', $contact->job_title);
        $this->assertSame('Dr. Ahmad', $contact->person->name);

        @unlink($tmpFile);
    }

    public function test_it_updates_existing_organization_and_skips_when_no_new_data(): void
    {
        $service = new OrganizationImportService();

        $org = Organization::create([
            'industry_id' => 1,
            'tier'        => 'A',
            'name'        => 'RS Pelita Sehat',
            'is_active'   => true,
            'address'     => null, // kosong, akan di-update oleh import
        ]);

        $csvContent = implode("\n", [
            'Nama Perusahaan,Industri,Kelas,Alamat,Status',
            'RS Pelita Sehat,Rumah Sakit,A,Jl. Pelita Harapan No. 5,Active',
        ]);

        $tmpFile = tempnam(sys_get_temp_dir(), 'test_import_') . '.csv';
        file_put_contents($tmpFile, $csvContent);

        $uploadedFile = new UploadedFile($tmpFile, 'test.csv', 'text/csv', null, true);

        // Import ke-1: Mengisi address yang kosong -> updated
        $result1 = $service->import($uploadedFile);
        $this->assertSame(1, $result1['updated']);

        $org->refresh();
        $this->assertSame('Jl. Pelita Harapan No. 5', $org->address);

        // Import ke-2: Data identik, tidak ada field kosong baru -> skipped
        $result2 = $service->import($uploadedFile);
        $this->assertSame(1, $result2['skipped']);

        @unlink($tmpFile);
    }

    protected function callPrivateMethod(object $object, string $method, array $arguments = [])
    {
        $reflection = new \ReflectionMethod($object, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $arguments);
    }
}
