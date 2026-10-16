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
        Schema::table('provinces', function (Blueprint $table) {
            $table->index('name', 'provinces_name_index');
        });

        Schema::table('regencies', function (Blueprint $table) {
            $table->index('name', 'regencies_name_index');
            $table->index(['province_id', 'name'], 'regencies_province_name_index');
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->index('name', 'districts_name_index');
            $table->index(['regency_id', 'name'], 'districts_regency_name_index');
        });

        Schema::table('villages', function (Blueprint $table) {
            $table->index('name', 'villages_name_index');
            $table->index(['district_id', 'name'], 'villages_district_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->dropIndex('villages_district_name_index');
            $table->dropIndex('villages_name_index');
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->dropIndex('districts_regency_name_index');
            $table->dropIndex('districts_name_index');
        });

        Schema::table('regencies', function (Blueprint $table) {
            $table->dropIndex('regencies_province_name_index');
            $table->dropIndex('regencies_name_index');
        });

        Schema::table('provinces', function (Blueprint $table) {
            $table->dropIndex('provinces_name_index');
        });
    }
};
