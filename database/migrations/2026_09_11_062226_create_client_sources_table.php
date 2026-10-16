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
        Schema::create('client_sources', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->string('name', 50)->unique();
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_sources');
    }
};
