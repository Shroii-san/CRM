<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandidat_id')->nullable()->unique()
                  ->constrained('kandidats')->nullOnDelete();

            $table->unsignedBigInteger('departemen_id')->nullable();

            $table->string('nama_lengkap');
            $table->string('nik', 20)->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->string('status_kerja', 30)->default('aktif'); // aktif/kontrak/tetap
            $table->string('foto')->nullable();
            $table->string('scan_ktp')->nullable();
            $table->string('cv')->nullable();
            $table->string('npwp')->nullable();
            $table->string('no_bpjs')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
