<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Compartilha o vocabulario de entrada do usuario para manter criacao e atualizacao alinhadas.
 */
abstract class UserFormRequest extends FormRequest
{
    /**
     * Autoriza o uso deste request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Retorna os dados do usuario prontos para persistencia.
     *
     * @return array<string, mixed>
     */
    public function validatedUserData(): array
    {
        $validatedData = $this->validated();

        // Na atualizacao, senha vazia significa "manter a atual", nao "apagar a senha".
        if (blank($validatedData['password'] ?? null)) {
            unset($validatedData['password']);
        }

        return $validatedData;
    }

    /**
     * Define as regras do nome.
     *
     * @return array<int, mixed>
     */
    protected function nameRules(): array
    {
        return [
            'required',
            'string',
            'min:3',
            'max:255',
            'regex:/^[\pL]+(?:\s[\pL]+)*$/u',
        ];
    }

    /**
     * Define as regras do username.
     *
     * @param array<int, mixed> $uniqueRules
     * @return array<int, mixed>
     */
    protected function usernameRules(array $uniqueRules): array
    {
        return [
            'required',
            'string',
            'min:3',
            'max:50',
            'regex:/^[A-Za-z0-9._-]+$/',
            ...$uniqueRules,
        ];
    }

    /**
     * Define as regras do email.
     *
     * @param array<int, mixed> $uniqueRules
     * @return array<int, mixed>
     */
    protected function emailRules(array $uniqueRules): array
    {
        return [
            'required',
            'string',
            'email:rfc',
            'max:255',
            ...$uniqueRules,
        ];
    }

    /**
     * Define as regras da senha para criacao ou atualizacao.
     *
     * @return array<int, mixed>
     */
    protected function passwordRules(bool $isRequired): array
    {
        return [
            $isRequired ? 'required' : 'nullable',
            'confirmed',
            Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
        ];
    }

    /**
     * Normaliza os campos textuais antes da validacao.
     */
    protected function prepareForValidation(): void
    {
        // Normalizar na borda evita poluir os controllers com limpeza de strings.
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'username' => strtolower(trim((string) $this->input('username'))),
            'email' => strtolower(trim((string) $this->input('email'))),
        ]);
    }
}
