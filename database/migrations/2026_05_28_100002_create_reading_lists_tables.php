<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('reading_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reading_list_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['reading_list_id', 'resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_list_items');
        Schema::dropIfExists('reading_lists');
    }
};
