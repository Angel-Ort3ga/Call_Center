<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

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
                'unique:users,username',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],

            'role_id' => [
                'required',
                'exists:roles,id',
            ],

            'departamento_id' => [
                'nullable',
                'exists:departamentos,id',
                Rule::requiredIf(function () {
                    return $this->esJefeDepartamento();
                }),
            ],

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

            'activo' => [
                'sometimes',
                'boolean',
            ],

        ];
    }

    protected function esJefeDepartamento(): bool
    {
        if (!$this->role_id) {
            return false;
        }

        return Role::where('id', $this->role_id)
            ->where('slug', 'jefe_departamento')
            ->exists();
    }

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

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'Debe ingresar un correo electrónico válido.',
            'unique' => 'Este :attribute ya está registrado.',
            'exists' => 'El :attribute seleccionado no existe.',
            'min' => 'La :attribute debe tener al menos :min caracteres.',
            'max' => 'La :attribute no puede tener más de :max caracteres.',
            'boolean' => 'El campo :attribute debe ser válido.',
        ];
    }
}
