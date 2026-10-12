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
        Schema::create('sales_visits', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedSmallInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->unsignedInteger('organizations_id')->nullable();
            $table->foreign('organizations_id')->references('id')->on('organizations')->onDelete('set null');

            $table->unsignedInteger('organization_contact_id')->nullable();
            $table->foreign('organization_contact_id')->references('id')->on('organization_contacts')->onDelete('set null');


            $table->unsignedInteger('attachment_id')->nullable();
            $table->foreign('attachment_id')->references('id')->on('attachment');

            $table->string('province_id', 20)->nullable();
            $table->foreign('province_id')->references('id')->on('provinces');

            $table->string('regency_id', 20)->nullable();
            $table->foreign('regency_id')->references('id')->on('regencies');

            $table->string('district_id', 20)->nullable();
            $table->foreign('district_id')->references('id')->on('districts');

            $table->string('village_id', 20)->nullable();
            $table->foreign('village_id')->references('id')->on('villages');


            $table->text('address')->nullable();
            $table->date('visit_date')->nullable();
            $table->string('visit_purpose', 255)->nullable();
            $table->boolean('is_follow_up')->default(false);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_visits');
    }
};
