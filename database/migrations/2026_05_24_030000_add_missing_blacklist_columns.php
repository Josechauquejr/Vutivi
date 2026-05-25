<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blacklist_words', function (Blueprint $table) {
            if (! Schema::hasColumn('blacklist_words', 'category')) {
                $table->string('category')->nullable()->after('word');
            }

            if (! Schema::hasColumn('blacklist_words', 'severity')) {
                $table->string('severity')->default('high')->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blacklist_words', function (Blueprint $table) {
            if (Schema::hasColumn('blacklist_words', 'severity')) {
                $table->dropColumn('severity');
            }

            if (Schema::hasColumn('blacklist_words', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
