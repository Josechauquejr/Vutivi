<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds lifecycle fields used by loan alerts, approvals and extensions.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'status')) {
                $table->string('status')->default('approved')->after('returned_at');
            }

            if (!Schema::hasColumn('reservations', 'extension_requested_at')) {
                $table->timestamp('extension_requested_at')->nullable()->after('extension_reason');
            }

            if (!Schema::hasColumn('reservations', 'extension_decision')) {
                $table->string('extension_decision')->nullable()->after('extension_requested_at');
            }

            if (!Schema::hasColumn('reservations', 'extension_decided_at')) {
                $table->timestamp('extension_decided_at')->nullable()->after('extension_decision');
            }

            if (!Schema::hasColumn('reservations', 'extension_decision_note')) {
                $table->text('extension_decision_note')->nullable()->after('extension_decided_at');
            }
        });

        Schema::table('physical_resources', function (Blueprint $table) {
            if (!Schema::hasColumn('physical_resources', 'allow_extension')) {
                $table->boolean('allow_extension')->default(true)->after('max_extensions');
            }
        });
    }

    /**
     * Reverts the lifecycle fields.
     */
    public function down(): void
    {
        Schema::table('physical_resources', function (Blueprint $table) {
            if (Schema::hasColumn('physical_resources', 'allow_extension')) {
                $table->dropColumn('allow_extension');
            }
        });

        Schema::table('reservations', function (Blueprint $table) {
            foreach (['extension_decision_note', 'extension_decided_at', 'extension_decision', 'extension_requested_at'] as $column) {
                if (Schema::hasColumn('reservations', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('reservations', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
