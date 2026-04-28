<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
             $table->id();
             $table->string('name');
             $table->string('description')->nullable();
             $table->timestamps();
         });
         
       Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('role_id')->constrained('roles');
            $table->boolean('active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });


        Schema::create('work_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('works', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('work_type_id')->constrained('work_types');

            $table->string('title');
            $table->text('synopsis')->nullable();
            $table->text('summary')->nullable();
            $table->string('isbn')->nullable();

            $table->integer('publication_year')->nullable();
            $table->string('language')->nullable();
            $table->string('status')->nullable();

            $table->integer('total_likes')->default(0);
            $table->integer('total_views')->default(0);
            $table->integer('total_downloads')->default(0);

            $table->timestamps();
        });

        Schema::create('files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('work_id')->constrained('works');

            $table->string('original_name');
            $table->string('path');
            $table->string('format');
            $table->bigInteger('size_bytes');

            $table->timestamp('uploaded_at')->nullable();

            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('biography')->nullable();
            $table->string('nationality')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories');

            $table->timestamps();
        });

        Schema::create('likes', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('work_id')->constrained('works');

            $table->timestamp('created_at')->useCurrent();

            $table->primary(['user_id', 'work_id']);
        });

        Schema::create('personal_library', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('work_id')->constrained('works');

            $table->string('tag')->nullable();
            $table->timestamp('added_at')->useCurrent();

            $table->primary(['user_id', 'work_id']);
        });

        Schema::create('transfers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('file_id')->constrained('files');
            $table->foreignId('user_id')->constrained('users');

            $table->string('origin_ip');
            $table->timestamp('transferred_at')->useCurrent();

            $table->timestamps();
        });

        Schema::create('author_work', function (Blueprint $table) {
            $table->foreignId('author_id')->constrained('authors');
            $table->foreignId('work_id')->constrained('works');

            $table->primary(['author_id', 'work_id']);
        });

        Schema::create('category_work', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('work_id')->constrained('works');

            $table->primary(['category_id', 'work_id']);
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_work');
        Schema::dropIfExists('author_work');
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('personal_library');
        Schema::dropIfExists('likes');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('files');
        Schema::dropIfExists('works');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('work_types');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
