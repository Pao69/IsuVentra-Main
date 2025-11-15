// src/stores/auth.js
import { defineStore } from 'pinia';
import api from "@/services/api";

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('token') || null,
        user: JSON.parse(localStorage.getItem('user') || 'null'),
    }),
    getters: {
        isLoggedIn: (state) => !!state.token,
        isAdmin: (state) => !!state.user && (state.user.is_admin === 1 || state.user.is_admin === true),
    },
    actions: {
        setAuth(user, token) {
            this.user = user;
            this.token = token;
            localStorage.setItem('user', JSON.stringify(user));
            localStorage.setItem('token', token);
        },
        async login(email, password) {
            // backend returns token on login
            const res = await api.post('/login', { email, password });
            // adapt keys depending on backend: e.g. res.data.access_token or res.data.token
            const token = res.data.access_token ?? res.data.token ?? null;
            const user = res.data.user ?? res.data;
            if (!token) throw new Error('No token returned from login');
            this.setAuth(user, token);
            return { user, token };
        },
        logout() {
            this.user = null;
            this.token = null;
            localStorage.removeItem('user');
            localStorage.removeItem('token');
        }
    }
});
