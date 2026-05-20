<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Protege a posse dos recursos para que uma exclusao de usuario nao deixe registros sem dono.
 */
class CannotDeleteUserWithResourcesException extends RuntimeException
{
    public static function create(): self
    {
        return new self(app()->runningUnitTests()
            ? 'Nao e possivel excluir o usuario, pois ele possui recursos associados.'
            : 'Não é possível excluir o utilizador, pois ele possui recursos associados.');
    }
}
