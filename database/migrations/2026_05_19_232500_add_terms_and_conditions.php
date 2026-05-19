<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria tabelas para termos e condições e rastreamento de aceitações.
     */
    public function up(): void
    {
        // Tabela de Termos e Condições
        Schema::create('terms_and_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->string('scope');  // 'requisition', 'pickup', 'extension', 'return'
            $table->string('title');
            $table->longText('content');
            $table->integer('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['resource_id', 'scope', 'version']);
            $table->index(['resource_id', 'scope', 'is_active']);
        });

        // Tabela de Aceitação de Termos
        Schema::create('term_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->string('term_scope');
            $table->timestamp('accepted_at')->useCurrent();

            $table->index('reservation_id');
            $table->index(['user_id', 'resource_id']);
        });

        // Adicionar colunas a reservations
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->integer('extension_count')->default(0);
            $table->integer('days_overdue')->default(0);
            $table->timestamp('picked_up_at')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->text('extension_reason')->nullable();
        });

        // Adicionar colunas a physical_resources
        Schema::table('physical_resources', function (Blueprint $table) {
            $table->boolean('requires_approval')->default(false);
            $table->integer('max_extensions')->default(2);
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        Schema::dropIfExists('term_acceptances');
        Schema::dropIfExists('terms_and_conditions');

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['approved_by']);
            $table->dropColumn([
                'approved_by',
                'approved_at',
                'extension_count',
                'days_overdue',
                'picked_up_at',
                'actual_return_date',
                'extension_reason'
            ]);
        });

        Schema::table('physical_resources', function (Blueprint $table) {
            $table->dropColumn([
                'requires_approval',
                'max_extensions'
            ]);
        });
    }
};
