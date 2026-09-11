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
        Schema::create('deal_stage_histories', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('deal_id');
            $table->foreign('deal_id')->references('id')->on('deals');

            $table->unsignedSmallInteger('from_stage_id');
            $table->foreign('from_stage_id')->references('id')->on('pipeline_stages');

            $table->unsignedSmallInteger('to_stage_id');
            $table->foreign('to_stage_id')->references('id')->on('pipeline_stages');

            $table->unsignedSmallInteger('changed_by');
            $table->foreign('changed_by')->references('id')->on('users');

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_stage_histories');
    }
};
