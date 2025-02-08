<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelOrder extends Model
{
    use SoftDeletes;
    
    /**
     * Campos que podem ser atribuídos em massa.
     * 
     * @var array
     */
    protected $fillable = [
        'user_id',
        'city',
        'state',
        'country',
        'departure_date',
        'return_date',
        'status',
    ];

    /**
     * Tipos de dados dos campos.
     * 
     * @var array
     */
    protected $casts = [
        'departure_date' => 'date',
        'return_date'    => 'date',
    ];

    /**
     * Relacionamento com o Usuário da Ordem de Viagem.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
