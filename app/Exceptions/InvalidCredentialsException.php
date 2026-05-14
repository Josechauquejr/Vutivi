<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Lancada quando o contrato de login e valido, mas a identidade ainda nao pode ser autenticada.
 */
class InvalidCredentialsException extends RuntimeException
{
    public static function create(): self
    {
        return new self('Usuario ou senha invalidos. Tente novamente.');
    }
}
