// src/stores/auth.js
import { defineStore } from 'pinia';
import api from "@/services/api";

export const useAuthStore = defineStore('auth', {
    state: () => {
        const token = localStorage.getItem('token') || null;
        const user = JSON.parse(localStorage.getItem('user') || 'null');
        let role = null;
        if (user) {
            role = (user.is_admin === 1 || user.is_admin === true) ? 'admin' : 'user';
        }
        return {
            token,
            user,
            role,
        };
    },
    getters: {
        isLoggedIn: (state) => !!state.token,
        isAdmin: (state) => state.role === 'admin',
        isUser: (state) => state.role === 'user',
    },
    actions: {
        setAuth(user, token) {
            this.user = user;
            this.token = token;

            // Set role based on user info
            this.role = (user?.is_admin === 1 || user?.is_admin === true) ? 'admin' : 'user';

            localStorage.setItem('user', JSON.stringify(user));
            localStorage.setItem('token', token);
        },
        async login(email, password) {
            const res = await api.post('/login', { email, password });
            const token = res.data.access_token ?? res.data.token ?? null;
            const user = res.data.user ?? res.data;

            if (!token) throw new Error('No token returned from login');
            this.setAuth(user, token);
            return { user, token };
        },
        logout() {
            this.user = null;
            this.token = null;
            this.role = null;
            localStorage.removeItem('user');
            localStorage.removeItem('token');
        }
    }
});
