<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            if (! Schema::hasColumn('resources', 'moderation_status')) {
                $table->string('moderation_status')->default('approved')->after('status');
            }

            if (! Schema::hasColumn('resources', 'moderation_score')) {
                $table->unsignedTinyInteger('moderation_score')->default(0)->after('moderation_status');
            }

            if (! Schema::hasColumn('resources', 'moderation_reason')) {
                $table->text('moderation_reason')->nullable()->after('moderation_score');
            }

            if (! Schema::hasColumn('resources', 'moderated_by')) {
                $table->foreignId('moderated_by')->nullable()->after('moderation_reason')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('resources', 'moderated_at')) {
                $table->timestamp('moderated_at')->nullable()->after('moderated_by');
            }

            if (! Schema::hasColumn('resources', 'moderation_auto')) {
                $table->boolean('moderation_auto')->default(true)->after('moderated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            foreach (['moderation_auto', 'moderated_at', 'moderation_reason', 'moderation_score', 'moderation_status'] as $column) {
                if (Schema::hasColumn('resources', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('resources', 'moderated_by')) {
                $table->dropConstrainedForeignId('moderated_by');
            }
        });
    }
};
