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
        Schema::create('attachment', function (Blueprint $table) {
            $table->increments('id');

            $table->string('file_name', 255);
            $table->text('description')->nullable();
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size'); // Ukuran dalam bytes
            $table->string('storage_reference', 255);

            $table->unsignedSmallInteger('uploaded_by');
            $table->foreign('uploaded_by')->references('id')->on('users');

            $table->string('attachable_type', 255);
            $table->unsignedInteger('attachable_id');

            $table->timestampsTz();

            $table->index(['attachable_type', 'attachable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachment');
    }
};
