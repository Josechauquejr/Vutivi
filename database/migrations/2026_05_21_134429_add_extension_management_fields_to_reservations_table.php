<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (! Schema::hasColumn('reservations', 'extension_decision')) {
                $table->string('extension_decision')->nullable();
            }

            if (! Schema::hasColumn('reservations', 'extension_decided_at')) {
                $table->timestamp('extension_decided_at')->nullable();
            }

            if (! Schema::hasColumn('reservations', 'extension_reviewed_by')) {
                $table->foreignId('extension_reviewed_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('reservations', 'extended_due_date')) {
                $table->date('extended_due_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'extension_reviewed_by')) {
                $table->dropConstrainedForeignId('extension_reviewed_by');
            }

            foreach (['extension_decision', 'extension_decided_at', 'extended_due_date'] as $column) {
                if (Schema::hasColumn('reservations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
