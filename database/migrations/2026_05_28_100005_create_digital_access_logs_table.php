<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 20); // view | download
            $table->string('ip', 45)->nullable();
            $table->timestamp('accessed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_access_logs');
    }
};
