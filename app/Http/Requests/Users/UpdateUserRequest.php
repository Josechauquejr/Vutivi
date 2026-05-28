<?php

namespace App\Http\Requests\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    private function routeUser(): User
    {
        return Auth::user();
    }
}
