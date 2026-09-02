import axios from 'axios';

const TOKEN_KEY = 'auth_token';
const USER_KEY = 'auth_user';

export function getToken() {
    return localStorage.getItem(TOKEN_KEY);
}

export function getUser() {
    const user = localStorage.getItem(USER_KEY);

    if (!user) {
        return null;
    }

    try {
        return JSON.parse(user);
    } catch (error) {
        console.error(
            'No se pudo recuperar el usuario:',
            error
        );

        return null;
    }
}

export function isAuthenticated() {
    return !!getToken();
}

export function iniciarSesion(token, user) {
    localStorage.setItem(
        TOKEN_KEY,
        token
    );

    localStorage.setItem(
        USER_KEY,
        JSON.stringify(user)
    );

    axios.defaults.headers.common[
        'Authorization'
    ] = `Bearer ${token}`;
}

export async function cerrarSesion() {
    try {
        await axios.post('/api/admin/logout');
    } catch (error) {
        console.error(
            'Error al cerrar sesión:',
            error
        );
    } finally {
        localStorage.removeItem(TOKEN_KEY);
        localStorage.removeItem(USER_KEY);

        delete axios.defaults.headers.common[
            'Authorization'
        ];
    }
}

export function restaurarSesion() {
    const token = getToken();

    if (!token) {
        return false;
    }

    axios.defaults.headers.common[
        'Authorization'
    ] = `Bearer ${token}`;

    return true;
}