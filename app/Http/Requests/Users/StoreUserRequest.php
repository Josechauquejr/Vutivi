<?php

namespace App\Http\Requests\Users;

/**
 * O cadastro e mais rigido que a atualizacao porque uma conta nova precisa nascer com senha.
 */
class StoreUserRequest extends UserFormRequest
{
    /**
     * Define as regras de criacao do usuario.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => $this->nameRules(),
            'username' => $this->usernameRules(['unique:users,username']),
            'email' => $this->emailRules(['unique:users,email']),
            'password' => $this->passwordRules(true),
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
