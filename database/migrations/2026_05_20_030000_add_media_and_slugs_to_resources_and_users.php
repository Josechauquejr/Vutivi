<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            if (! Schema::hasColumn('resources', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('title');
            }

            if (! Schema::hasColumn('resources', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('description');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('username');
            }

            if (! Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('email');
            }
        });

        if (Schema::hasTable('resources')) {
            DB::table('resources')->whereNull('slug')->orderBy('id')->get(['id', 'title'])->each(function ($resource) {
                $base = Str::slug($resource->title) ?: 'recurso';
                $slug = $base;
                $index = 2;

                while (DB::table('resources')->where('slug', $slug)->where('id', '<>', $resource->id)->exists()) {
                    $slug = "{$base}-{$index}";
                    $index++;
                }

                DB::table('resources')->where('id', $resource->id)->update(['slug' => $slug]);
            });
        }

        if (Schema::hasTable('users')) {
            DB::table('users')->whereNull('slug')->orderBy('id')->get(['id', 'username', 'name'])->each(function ($user) {
                $base = Str::slug($user->username ?: $user->name) ?: 'utilizador';
                $slug = $base;
                $index = 2;

                while (DB::table('users')->where('slug', $slug)->where('id', '<>', $user->id)->exists()) {
                    $slug = "{$base}-{$index}";
                    $index++;
                }

                DB::table('users')->where('id', $user->id)->update(['slug' => $slug]);
            });
        }
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            if (Schema::hasColumn('resources', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }

            if (Schema::hasColumn('resources', 'cover_image')) {
                $table->dropColumn('cover_image');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }

            if (Schema::hasColumn('users', 'profile_photo')) {
                $table->dropColumn('profile_photo');
            }
        });
    }
};
