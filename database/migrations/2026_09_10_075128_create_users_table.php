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
        Schema::create('users', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->unsignedSmallInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles');

            $table->string('name', 255);
            $table->string('email', 255)->nullable()->unique();
            $table->string('phone', 20)->nullable()->unique();
            $table->string('password_hash', 255);
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestampTz('last_activity_at')->nullable();

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
