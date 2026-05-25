<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates the user-resource favorites pivot.
     */
    public function up(): void
    {
        if (Schema::hasTable('resource_user_favorites')) {
            return;
        }

        Schema::create('resource_user_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'resource_id']);
        });
    }

    /**
     * Drops the favorites pivot.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_user_favorites');
    }
};
