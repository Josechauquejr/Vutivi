<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Ponto central para registros compartilhados da aplicacao.
 * Mantemos esta classe enxuta para que futuros registros globais sejam faceis de localizar.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra servicos compartilhados da aplicacao.
     */
    public function register(): void
    {
    }

    /**
     * Inicializa configuracoes globais apos a partida da aplicacao.
     */
    public function boot(): void
    {
    }
}
