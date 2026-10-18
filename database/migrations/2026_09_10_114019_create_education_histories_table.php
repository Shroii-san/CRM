<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('education_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->nullable()
                ->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()
                ->constrained()->cascadeOnDelete();

            $table->string('level', 20);           // SD, SMP, SMA, SMK, MAN, D3, S1, dll
            $table->string('school_name');
            $table->string('major')->nullable();
            $table->string('degree')->nullable();  // Contoh: S.Kom., S.E.
            $table->string('score', 15)->nullable(); // IPK atau nilai rata-rata Ijazah
            $table->string('last_school_location')->nullable();
            $table->unsignedSmallInteger('start_year');
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('education_histories');
    }
};
