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
        Schema::create('menus', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->unsignedSmallInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('menus');

            $table->unsignedSmallInteger('icon_id')->nullable();
            $table->foreign('icon_id')->references('id')->on('menu_icons');

            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique()->nullable();
            $table->string('route', 50)->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
