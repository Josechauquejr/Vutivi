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

        if ($this->hasFile('profile_photo')) {
            $validatedData['profile_photo'] = $this->file('profile_photo')->store('profile-photos', 'public');
        } else {
            unset($validatedData['profile_photo']);
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
            'name.required'            => 'O nome é obrigatório.',
            'name.min'                 => 'O nome deve ter pelo menos :min caracteres.',
            'name.max'                 => 'O nome não pode ter mais de :max caracteres.',
            'name.regex'               => 'O nome deve conter apenas letras e espaços simples entre palavras.',
            'username.required'        => 'O username é obrigatório.',
            'username.min'             => 'O username deve ter pelo menos :min caracteres.',
            'username.max'             => 'O username não pode ter mais de :max caracteres.',
            'username.regex'           => 'O username só pode conter letras, números, ponto, hífen e underscore.',
            'username.unique'          => 'Este username já está em uso.',
            'email.required'           => 'O endereço de email é obrigatório.',
            'email.email'              => 'Introduza um endereço de email válido.',
            'email.max'                => 'O email não pode ter mais de :max caracteres.',
            'email.unique'             => 'Este email já está associado a uma conta.',
            'password.required'        => 'A password é obrigatória.',
            'password.confirmed'       => 'A confirmação da password não corresponde.',
            'password.min'             => 'A password deve ter pelo menos :min caracteres.',
            'password.letters'         => 'A password deve conter pelo menos uma letra.',
            'password.mixed'           => 'A password deve conter letras maiúsculas e minúsculas.',
            'password.numbers'         => 'A password deve conter pelo menos um número.',
            'password.symbols'         => 'A password deve conter pelo menos um símbolo (ex: !, @, #).',
            'profile_photo.image'      => 'O ficheiro da foto de perfil deve ser uma imagem.',
            'profile_photo.max'        => 'A foto de perfil não pode exceder 2 MB.',
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
            'profile_photo' => 'foto de perfil',
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
