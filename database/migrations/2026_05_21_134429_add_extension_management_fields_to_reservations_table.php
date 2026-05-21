<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('reservations', function (Blueprint $table) {

        // Já existente:
        // extension_requested_at

        // Novas colunas

        $table->string('extension_decision')->nullable();
        // approved | rejected

        $table->timestamp('extension_decided_at')->nullable();

        $table->foreignId('extension_reviewed_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        $table->date('extended_due_date')->nullable();
    });
}

   public function down(): void
{
    Schema::table('reservations', function (Blueprint $table) {

        $table->dropConstrainedForeignId('extension_reviewed_by');

        $table->dropColumn([
            'extension_decision',
            'extension_decided_at',
            'extended_due_date',
        ]);
    });
}
};
