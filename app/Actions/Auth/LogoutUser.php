<?php

namespace App\Actions\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Centraliza o logout para que toda saida limpe o estado da sessao da mesma forma.
 */
class LogoutUser
{
    /**
     * Encerra a sessao autenticada e limpa os dados sensiveis da sessao atual.
     */
    public function handle(Request $request): void
    {
        Auth::logout();

        // Invalidamos a sessao e giramos o token para evitar reaproveitamento acidental.
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
