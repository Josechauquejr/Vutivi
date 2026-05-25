<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blacklist_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->string('category')->nullable();
            $table->string('severity')->default('high');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blacklist_words');
    }
};
