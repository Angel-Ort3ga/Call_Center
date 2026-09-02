<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Departamento;

class Llamada extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'fecha',
        'hora',
        'telefono',
        'nombre',
        'motivo',
        'categoria',
        'departamento_id',
        'usuario_id',
        'estado',
        'observaciones',
        'transferida_at',
        'finalizada_at',
    ];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
        'transferida_at' => 'datetime',
        'finalizada_at' => 'datetime',
    ];

    /**
     * Departamento al que pertenece la llamada.
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(
            Departamento::class,
            'departamento_id'
        );
    }



    /**
     * Usuario que registró la llamada.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'usuario_id'
        );
    }
}
