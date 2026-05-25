<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'banned_at')) {
                $table->timestamp('banned_at')->nullable()->after('role');
            }

            if (! Schema::hasColumn('users', 'ban_reason')) {
                $table->text('ban_reason')->nullable()->after('banned_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'ban_reason')) {
                $table->dropColumn('ban_reason');
            }

            if (Schema::hasColumn('users', 'banned_at')) {
                $table->dropColumn('banned_at');
            }
        });
    }
};
