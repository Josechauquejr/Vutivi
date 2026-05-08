<?php

namespace App\Actions\Users;

use App\Exceptions\CannotDeleteUserWithResourcesException;
use App\Models\User;

/**
 * Exclui um usuario apenas quando isso nao deixara recursos cadastrados sem dono.
 */
class DeleteUser
{
    /**
     * Exclui o usuario quando o dominio permite a operacao.
     */
    public function handle(User $user): void
    {
        if ($user->resources()->exists()) {
            // A posse do recurso faz parte do contrato do dominio, por isso falhamos de forma explicita.
            throw CannotDeleteUserWithResourcesException::create();
        }

        $user->delete();
    }
}
