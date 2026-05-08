<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Define o menor contrato necessario para o login antes de chegar a acao de dominio.
 */
class LoginRequest extends FormRequest
{
    /**
     * Autoriza o uso deste request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define as regras basicas do formulario de login.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function credentials(): array
    {
        // A acao deve depender de um contrato pequeno de dados, nao do request HTTP inteiro.
        return $this->only('username', 'password');
    }

    protected function prepareForValidation(): void
    {
        // Normalizamos na borda para que validacao e autenticacao usem a mesma identidade.
        $this->merge([
            'username' => strtolower(trim((string) $this->input('username'))),
        ]);
    }
}
