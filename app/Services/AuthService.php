<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Intenta autenticar al usuario.
     */
    public function login(string $email, string $password)
    {
        $user = User::with(['role', 'departamento'])
            ->where('email', $email)
            ->where('activo', true)
            ->first();

        if (!$user) {
            return null;
        }

        if (!Hash::check($password, $user->password)) {
            return null;
        }

        // Elimina tokens anteriores
        $user->tokens()->delete();

        // Genera nuevo token
        $token = $user->createToken('admin')->plainTextToken;

        // Actualiza último acceso
        $user->update([
            'ultimo_acceso' => now()
        ]);

        return [
            'token' => $token,
            'user' => $user
        ];
    }

    /**
     * Cerrar sesión.
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
