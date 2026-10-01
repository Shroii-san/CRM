<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keluarga', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('status', ['Orang Tua', 'Suami', 'Istri', 'Anak']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keluarga');
    }
};
