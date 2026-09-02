<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Autorizar petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [

            /*
            |--------------------------------------------------------------------------
            | Información personal
            |--------------------------------------------------------------------------
            */

            'nombre' => [
                'required',
                'string',
                'max:100',
            ],

            'apellido' => [
                'required',
                'string',
                'max:100',
            ],

            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')
                    ->ignore($user?->id),
            ],

            /*
            |--------------------------------------------------------------------------
            | Acceso
            |--------------------------------------------------------------------------
            */

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user?->id),
            ],

            /*
            |--------------------------------------------------------------------------
            | Contraseña
            |--------------------------------------------------------------------------
            |
            | Es opcional al editar.
            | Si se deja vacía, se conserva la contraseña actual.
            |
            */

            'password' => [
                'nullable',
                'string',
                'min:8',
            ],

            /*
            |--------------------------------------------------------------------------
            | Rol
            |--------------------------------------------------------------------------
            */

            'role_id' => [
                'required',
                'exists:roles,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Departamento
            |--------------------------------------------------------------------------
            |
            | Obligatorio únicamente para Jefe de departamento.
            |
            */

            'departamento_id' => [
                'nullable',
                'exists:departamentos,id',
                Rule::requiredIf(function () {
                    return $this->esJefeDepartamento();
                }),
            ],

            /*
            |--------------------------------------------------------------------------
            | Información laboral
            |--------------------------------------------------------------------------
            */

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'extension' => [
                'nullable',
                'string',
                'max:10',
            ],

            /*
            |--------------------------------------------------------------------------
            | Estado
            |--------------------------------------------------------------------------
            */

            'activo' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Determinar si el rol seleccionado es
     * Jefe de departamento.
     */
    protected function esJefeDepartamento(): bool
    {
        if (!$this->role_id) {
            return false;
        }

        return Role::where('id', $this->role_id)
            ->where('slug', 'jefe_departamento')
            ->exists();
    }

    /**
     * Nombres amigables.
     */
    public function attributes(): array
    {
        return [

            'nombre' => 'nombre',

            'apellido' => 'apellido',

            'username' => 'nombre de usuario',

            'email' => 'correo electrónico',

            'password' => 'contraseña',

            'role_id' => 'rol',

            'departamento_id' => 'departamento',

            'telefono' => 'teléfono',

            'extension' => 'extensión',

            'activo' => 'estado',
        ];
    }

    /**
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [

            'required' =>
            'El campo :attribute es obligatorio.',

            'email' =>
            'Debe ingresar un correo electrónico válido.',

            'unique' =>
            'Este :attribute ya está registrado.',

            'exists' =>
            'El :attribute seleccionado no existe.',

            'min' =>
            'La :attribute debe tener al menos :min caracteres.',

            'max' =>
            'La :attribute no puede tener más de :max caracteres.',

            'boolean' =>
            'El campo :attribute debe ser válido.',
        ];
    }
}
