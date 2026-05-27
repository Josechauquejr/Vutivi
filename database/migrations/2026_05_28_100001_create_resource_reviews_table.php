<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('stars')->unsigned();
            $table->text('body')->nullable();
            $table->timestamps();

            $table->unique(['resource_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_reviews');
    }
};
