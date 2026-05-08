<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['physical', 'digital']);
            $table->enum('status', ['available', 'reserved', 'active'])->default('available');
            $table->integer('quantity_available')->default(1);
            $table->foreignId('owner_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('physical_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->unique()->constrained('resources')->cascadeOnDelete();
            $table->string('location');
            $table->unsignedInteger('max_loan_days');
            $table->string('condition');
        });

        Schema::create('digital_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->unique()->constrained('resources')->cascadeOnDelete();
            $table->string('file_path');
            $table->enum('access_type', ['download', 'view']);
            $table->unsignedInteger('access_days');
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['physical', 'digital']);
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverte as migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('digital_resources');
        Schema::dropIfExists('physical_resources');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('resources');
        Schema::dropIfExists('users');
    }
};
