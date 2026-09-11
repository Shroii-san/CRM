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
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->unsignedInteger('deal_id')->nullable();
            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');

            $table->unsignedSmallInteger('stage_task_template_id')->nullable();
            $table->foreign('stage_task_template_id')->references('id')->on('stage_task_templates')->onDelete('set null');

            $table->unsignedSmallInteger('assigned_user_id');
            $table->foreign('assigned_user_id')->references('id')->on('users');

            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->date('due_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->unsignedSmallInteger('status')->default(1);
            $table->unsignedSmallInteger('priority')->default(1);

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
