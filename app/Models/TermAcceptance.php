<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Rastreamento de aceitação de termos e condições pelos usuários.
 * Registra cada aceitação com timestamp para auditoria.
 */
class TermAcceptance extends Model
{
    protected $table = 'term_acceptances';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'resource_id',
        'reservation_id',
        'term_scope'
    ];

    protected $casts = [
        'accepted_at' => 'datetime'
    ];

    /**
     * Retorna o usuário que aceitou os termos.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retorna a reserva associada.
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Retorna o recurso.
     */
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    /**
     * Verifica se um usuário aceitou um termo específico em uma reserva.
     */
    public static function hasAccepted($userId, $reservationId, $scope)
    {
        return self::where('user_id', $userId)
            ->where('reservation_id', $reservationId)
            ->where('term_scope', $scope)
            ->exists();
    }

    /**
     * Verifica se um usuário aceitou o termo de um recurso (antes de reservar).
     */
    public static function hasAcceptedResourceTerm($userId, $resourceId, $scope)
    {
        return self::where('user_id', $userId)
            ->where('resource_id', $resourceId)
            ->where('term_scope', $scope)
            ->whereNull('reservation_id')
            ->exists();
    }
}
