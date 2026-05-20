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
            if (Schema::hasColumn('reservations', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
