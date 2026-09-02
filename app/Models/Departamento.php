<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'extension',
        'telefono',
        'correo',
        'responsable_id',
        'horario_inicio',
        'horario_fin',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Responsable del departamento
    |--------------------------------------------------------------------------
    */

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'responsable_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Usuarios pertenecientes al departamento
    |--------------------------------------------------------------------------
    */

    public function users(): HasMany
    {
        return $this->hasMany(
            User::class,
            'departamento_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Llamadas del departamento
    |--------------------------------------------------------------------------
    */

    public function llamadas(): HasMany
    {
        return $this->hasMany(
            Llamada::class,
            'departamento_id'
        );
    }
}
