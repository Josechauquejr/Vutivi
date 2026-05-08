<?php

namespace App\Actions\Auth;

use App\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Auth;

/**
 * Mantem a autenticacao como uma acao pequena de dominio para que o controller cuide apenas do HTTP.
 */
class AuthenticateUser
{
    /**
     * Tenta autenticar o usuario com as credenciais recebidas.
     *
     * @param array<string, mixed> $credentials
     */
    public function handle(array $credentials): void
    {
        if (! Auth::attempt($credentials)) {
            // Lancar a excecao preserva o fluxo feliz linear e deixa a camada HTTP decidir como mostrar a falha.
            throw InvalidCredentialsException::create();
        }
    }
}
