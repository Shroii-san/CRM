<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name');
            $table->string('nik', 16);
            $table->string('phone_number', 20);
            $table->string('division');
            $table->date('application_date');
            $table->string('npwp')->nullable();
            $table->string('no_bpjs')->nullable();

            // 1 = pending, 2 = accepted, 3 = rejected
            $table->unsignedSmallInteger('status')->default(1);

            // kolom foto, scan_ktp, cv akan menggunakan polymorphic attachments.
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
