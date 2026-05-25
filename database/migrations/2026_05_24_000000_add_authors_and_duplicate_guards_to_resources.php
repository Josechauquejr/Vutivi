<?php

use App\Models\Resource;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            if (! Schema::hasColumn('resources', 'authors')) {
                $table->text('authors')->nullable()->after('title');
            }

            if (! Schema::hasColumn('resources', 'title_normalized')) {
                $table->string('title_normalized')->nullable()->after('title');
            }
        });

        if (Schema::hasColumn('resources', 'title_normalized')) {
            $usedTitles = [];

            DB::table('resources')->orderBy('id')->get(['id', 'title'])->each(function ($resource) use (&$usedTitles) {
                $normalized = Resource::normalizeTitle((string) $resource->title);
                $uniqueValue = $normalized;

                if ($uniqueValue !== '' && isset($usedTitles[$uniqueValue])) {
                    $uniqueValue = $uniqueValue.' #'.$resource->id;
                }

                if ($uniqueValue !== '') {
                    $usedTitles[$uniqueValue] = true;
                }

                DB::table('resources')
                    ->where('id', $resource->id)
                    ->update(['title_normalized' => $uniqueValue ?: 'recurso-'.$resource->id]);
            });

            Schema::table('resources', function (Blueprint $table) {
                $table->unique('title_normalized');
            });
        }

        Schema::table('digital_resources', function (Blueprint $table) {
            if (! Schema::hasColumn('digital_resources', 'file_hash')) {
                $table->string('file_hash', 64)->nullable()->after('file_path');
            }
        });

        if (Schema::hasColumn('digital_resources', 'file_hash')) {
            $usedHashes = [];

            DB::table('digital_resources')->whereNull('file_hash')->orderBy('id')->get(['id', 'file_path'])->each(function ($digitalResource) use (&$usedHashes) {
                $path = (string) $digitalResource->file_path;

                if ($path === '' || ! Storage::exists($path)) {
                    return;
                }

                $hash = hash_file('sha256', Storage::path($path));

                if (! $hash || isset($usedHashes[$hash])) {
                    return;
                }

                $usedHashes[$hash] = true;

                DB::table('digital_resources')
                    ->where('id', $digitalResource->id)
                    ->update(['file_hash' => $hash]);
            });

            Schema::table('digital_resources', function (Blueprint $table) {
                $table->unique('file_hash');
            });
        }
    }

    public function down(): void
    {
        Schema::table('digital_resources', function (Blueprint $table) {
            if (Schema::hasColumn('digital_resources', 'file_hash')) {
                $table->dropUnique(['file_hash']);
                $table->dropColumn('file_hash');
            }
        });

        Schema::table('resources', function (Blueprint $table) {
            if (Schema::hasColumn('resources', 'title_normalized')) {
                $table->dropUnique(['title_normalized']);
                $table->dropColumn('title_normalized');
            }

            if (Schema::hasColumn('resources', 'authors')) {
                $table->dropColumn('authors');
            }
        });
    }
};
