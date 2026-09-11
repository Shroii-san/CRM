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
        Schema::create('deals', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');

            $table->unsignedSmallInteger('pipeline_id');
            $table->foreign('pipeline_id')->references('id')->on('pipelines');

            $table->unsignedSmallInteger('current_stage_id');
            $table->foreign('current_stage_id')->references('id')->on('pipeline_stages');

            $table->unsignedSmallInteger('assigned_user_id');
            $table->foreign('assigned_user_id')->references('id')->on('users');

            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->char('currency', 3)->default('IDR');
            $table->decimal('value', 15, 2)->default(0.00);
            $table->unsignedSmallInteger('status')->default(1);
            $table->unsignedSmallInteger('priority')->default(1);
            $table->date('expected_close_at');
            $table->date('actual_closed_at')->nullable();
            $table->boolean('is_active')->default(true);


            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
