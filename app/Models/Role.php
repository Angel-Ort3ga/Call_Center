<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Role extends Model
{
    use HasFactory;

    /**
     * Campos asignables.
     */
    protected $fillable = [

        'nombre',

        'codigo',

        'extension',

        'correo',

        'responsable_id',

        'horario_inicio',

        'horario_fin',

        'activo'

    ];

    /**
     * Conversión de tipos.
     */
    protected function casts(): array
    {
        return [

            'activo' => 'boolean',

            'horario_inicio' => 'datetime:H:i',

            'horario_fin' => 'datetime:H:i',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Un departamento tiene muchos usuarios.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Usuario responsable del departamento.
     */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}
