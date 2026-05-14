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
     * Mensagens amigaveis para os formularios de usuario.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Informe o nome.',
            'name.min' => 'O nome deve ter pelo menos :min caracteres.',
            'name.regex' => 'O nome deve conter apenas letras e espacos.',
            'username.required' => 'Informe o username.',
            'username.min' => 'O username deve ter pelo menos :min caracteres.',
            'username.regex' => 'O username pode conter apenas letras, numeros, ponto, hifen e underscore.',
            'username.unique' => 'Este username ja esta cadastrado.',
            'email.required' => 'Informe o email.',
            'email.email' => 'Informe um email valido.',
            'email.unique' => 'Este email ja esta cadastrado.',
            'password.required' => 'Informe a password.',
            'password.confirmed' => 'A confirmacao da password nao corresponde.',
            'password.min' => 'A password deve ter pelo menos :min caracteres.',
            'password.letters' => 'A password deve conter pelo menos uma letra.',
            'password.mixed' => 'A password deve conter letras maiusculas e minusculas.',
            'password.numbers' => 'A password deve conter pelo menos um numero.',
            'password.symbols' => 'A password deve conter pelo menos um simbolo.',
        ];
    }

    /**
     * Nomes dos campos apresentados nas mensagens geradas pelo Laravel.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'username' => 'username',
            'email' => 'email',
            'password' => 'password',
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
