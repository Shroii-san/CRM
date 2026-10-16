<?php

namespace Tests\Feature;

use App\Models\Industry;
use App\Models\Organization;
use App\Services\organization\OrganizationExportService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class OrganizationExportServiceTest extends TestCase
{
    protected OrganizationExportService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('organizations');
        Schema::dropIfExists('industries');

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

        $this->service = app(OrganizationExportService::class);
    }

    public function test_it_rejects_invalid_export_format(): void
    {
        request()->replace(['format' => 'json']);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Format export tidak valid');

        $this->service->exportExcel();
    }

    public function test_it_exports_csv_with_filters(): void
    {
        $industry = Industry::create([
            'name' => 'Rumah Sakit',
            'is_active' => true,
        ]);

        Organization::create([
            'industry_id' => $industry->id,
            'tier' => 'A',
            'name' => 'RS Sehat Sentosa',
            'email' => 'info@rssehat.test',
            'phone' => '0811000001',
            'description' => 'Deskripsi RS',
            'is_active' => true,
            'address' => 'Jl. Sehat No. 1',
        ]);

        Organization::create([
            'industry_id' => $industry->id,
            'tier' => 'B',
            'name' => 'RS Bintang Medika',
            'email' => 'info@rsbintang.test',
            'phone' => '0811000002',
            'description' => 'Deskripsi RS 2',
            'is_active' => false,
            'address' => 'Jl. Bintang No. 2',
        ]);

        request()->replace([
            'format' => 'csv',
            'industry_id' => $industry->id,
            'status' => 'active',
        ]);

        $response = $this->service->exportExcel();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('organizations-export-', $response->headers->get('Content-Disposition'));
    }

    public function test_it_exports_xlsx_file(): void
    {
        $industry = Industry::create([
            'name' => 'Klinik',
            'is_active' => true,
        ]);

        Organization::create([
            'industry_id' => $industry->id,
            'tier' => 'C',
            'name' => 'Klinik Nusantara',
            'email' => 'info@kliniknusantara.test',
            'phone' => '0812000001',
            'description' => 'Klinik test',
            'is_active' => true,
            'address' => 'Jl. Nusantara No. 1',
        ]);

        request()->replace([
            'format' => 'xlsx',
            'tier' => 'C',
        ]);

        $response = $this->service->exportExcel();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('xlsx', strtolower($response->headers->get('Content-Disposition', '')));
    }
}
