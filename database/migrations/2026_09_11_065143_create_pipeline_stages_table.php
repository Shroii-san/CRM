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
        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->unsignedSmallInteger('pipeline_id');
            $table->foreign('pipeline_id')->references('id')->on('pipelines')->onDelete('cascade');

            $table->string('name', 50);
            $table->string('slug', 50)->nullable();
            $table->string('description', 255)->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('is_terminal')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestampsTz();

            $table->unique(['pipeline_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_stages');
    }
};
