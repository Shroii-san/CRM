<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('candidate_id')->nullable()->unique()
                ->constrained('candidates')->nullOnDelete();

            $table->unsignedBigInteger('department_id')->nullable();

            $table->string('full_name');
            $table->string('nik', 16)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->date('join_date')->nullable();
            $table->string('npwp')->nullable();
            $table->string('no_bpjs')->nullable();

            // 1 = active, 2 = contract, 3 = permanent
            $table->unsignedSmallInteger('status')->default(1);

            // kolom foto, scan_ktp, cv akan menggunakan polymorphic attachments.
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
