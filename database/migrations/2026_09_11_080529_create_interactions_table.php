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
        Schema::create('interactions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->unsignedInteger('deal_id')->nullable();
            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');

            $table->unsignedInteger('organization_contact_id')->nullable();
            $table->foreign('organization_contact_id')->references('id')->on('organization_contacts')->onDelete('set null');

            $table->unsignedSmallInteger('type')->default(1); // 1 = chat, 2 = email, 3 = phone
            $table->string('subject', 255);
            $table->text('description')->nullable();
            $table->text('summary')->nullable();
            $table->unsignedSmallInteger('status')->default(1); // 1 = scheduled, 2 = completed, 3 = cancelled

            $table->timestampTz('start_at')->nullable();
            $table->timestampTz('end_at')->nullable();

            $table->unsignedSmallInteger('performed_by');
            $table->foreign('performed_by')->references('id')->on('users');

            $table->string('external_reference', 255)->nullable();

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
