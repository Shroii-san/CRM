<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE keluarga MODIFY status ENUM('Orang Tua', 'Ayah', 'Ibu', 'Suami', 'Istri', 'Anak') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('keluarga')->whereIn('status', ['Ayah', 'Ibu'])->update(['status' => 'Orang Tua']);
            DB::statement("ALTER TABLE keluarga MODIFY status ENUM('Orang Tua', 'Suami', 'Istri', 'Anak') NOT NULL");
        }
    }
};