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
        Schema::create('external_conversations', function (Blueprint $table) {
            $table->increments('id');

            $table->string('platform', 50);
            $table->string('external_conversation_id', 255);

            $table->unsignedInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->timestampTz('last_interaction_at')->nullable();
            $table->jsonb('metadata')->nullable();

            $table->timestampsTz();

            $table->unique(['platform', 'external_conversation_id'], 'uk_platform_ext_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_conversations');
    }
};
