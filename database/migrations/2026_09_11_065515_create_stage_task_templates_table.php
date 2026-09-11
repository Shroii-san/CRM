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
        Schema::create('stage_task_templates', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->unsignedSmallInteger('stage_id');
            $table->foreign('stage_id')->references('id')->on('pipeline_stages')->onDelete('cascade');

            $table->string('name', 255);
            $table->string('description', 255)->nullable();
            $table->unsignedSmallInteger('priority')->default(1);
            $table->unsignedSmallInteger('due_offset_days')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage_task_templates');
    }
};
