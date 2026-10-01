<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keluarga', function (Blueprint $table) {
            $table->foreignId('karyawan_id')->nullable()->change();
            $table->foreignId('kandidat_id')->nullable()->after('karyawan_id')
                ->constrained('kandidats')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('keluarga', function (Blueprint $table) {
            $table->dropForeign(['kandidat_id']);
            $table->dropColumn('kandidat_id');
            $table->foreignId('karyawan_id')->nullable(false)->change();
        });
    }
};
