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
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedSmallInteger('industry_id');
            $table->foreign('industry_id')->references('id')->on('industries');

            $table->char('tier', 1)->default('A');
            $table->string('name', 255)->unique();
            $table->string('email', 255)->nullable()->unique();
            $table->string('phone', 20)->nullable()->unique();
            $table->string('website', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('address')->nullable();

            $table->string('province_id', 20)->nullable();
            $table->foreign('province_id')->references('id')->on('provinces');

            $table->string('regency_id', 20)->nullable();
            $table->foreign('regency_id')->references('id')->on('regencies');

            $table->string('district_id', 20)->nullable();
            $table->foreign('district_id')->references('id')->on('districts');

            $table->string('village_id', 20)->nullable();
            $table->foreign('village_id')->references('id')->on('villages');

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
