<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('family_members', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('employee_id')->nullable()
                ->constrained('employees')->cascadeOnDelete();
            $table->foreignId('candidate_id')->nullable()
                ->constrained('candidates')->cascadeOnDelete();

            $table->string('name');
            $table->string('birth_place');
            $table->date('birth_date');

            // 1 = parent, 2 = spouse, 3 = child
            $table->unsignedSmallInteger('status')->default(1);
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
