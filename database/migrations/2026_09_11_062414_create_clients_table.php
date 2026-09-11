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
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('person_id')->nullable();
            $table->foreign('person_id')->references('id')->on('persons');

            $table->unsignedInteger('organization_id')->nullable();
            $table->foreign('organization_id')->references('id')->on('organizations');

            $table->unsignedSmallInteger('source_id');
            $table->foreign('source_id')->references('id')->on('client_sources');

            $table->boolean('is_active')->default(true);

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
