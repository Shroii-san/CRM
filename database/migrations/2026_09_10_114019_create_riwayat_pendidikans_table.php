<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_pendidikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kandidat_id')->nullable()
                  ->constrained('kandidats')->cascadeOnDelete();
            $table->foreignId('karyawan_id')->nullable()
                  ->constrained('karyawans')->cascadeOnDelete();

            $table->string('jenjang', 20);          // SD, SMP, SMA/SMK, D3, S1, dll
            $table->string('nama_sekolah');
            $table->string('jurusan')->nullable();
            $table->string('gelar')->nullable();            // Contoh: S.Kom., S.E.
            $table->string('ipk_atau_nilai', 15)->nullable(); // IPK atau nilai rata-rata
            $table->string('lokasi')->nullable();           // Kota/wilayah institusi
            $table->integer('tahun_masuk');
            $table->integer('tahun_lulus')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_pendidikans');
    }
};