<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Role;
use App\Models\Departamento;
use App\Models\Llamada;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [

        // Información personal
        'nombre',
        'apellido',
        'username',

        // Acceso
        'email',
        'password',

        // Información laboral
        'telefono',
        'extension',
        'foto',

        // Relaciones
        'role_id',
        'departamento_id',

        // Estado
        'activo',
        'ultimo_acceso',
        'new_notifications'
    ];

    /**
     * Campos ocultos.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversión automática de tipos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
            'ultimo_acceso' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Un usuario pertenece a un rol.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Un usuario pertenece a un departamento.
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function llamadas(): HasMany
    {
        return $this->hasMany(
            Llamada::class,
            'usuario_id'
        );
    }
    /**
     * Nombre completo del usuario.
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    public function tieneRol(string $rol): bool
    {
        return $this->role?->slug === $rol;
    }

    public function esSuperAdmin(): bool
    {
        return $this->tieneRol('super_admin');
    }

    public function esSupervisor(): bool
    {
        return $this->tieneRol('supervisor');
    }

    public function esJefeDepartamento(): bool
    {
        return $this->tieneRol('jefe_departamento');
    }

    public function esRecepcionista(): bool
    {
        return $this->tieneRol('recepcionista');
    }
}
