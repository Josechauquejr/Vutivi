<?php

namespace App\Http\Requests\Users;

use App\Models\User;
use Illuminate\Validation\Rule;

/**
 * A atualizacao aceita os valores atuais do usuario sem abrir mao da unicidade no restante do sistema.
 */
class UpdateUserRequest extends UserFormRequest
{
    /**
     * Define as regras de atualizacao do usuario.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $user = $this->routeUser();

        return [
            'name' => $this->nameRules(),
            'username' => $this->usernameRules([
                Rule::unique('users', 'username')->ignore($user->id),
            ]),
            'email' => $this->emailRules([
                Rule::unique('users', 'email')->ignore($user->id),
            ]),
            'password' => $this->passwordRules(false),
            'profile_photo' => ['nullable', 'image', 'max:4096'],
        ];
    }

    private function routeUser(): User
    {
        // O model binding da rota informa as regras de unicidade sobre qual registro esta sendo editado.
        return $this->route('user');
    }
}
