<template>
  <div class="auth-page">
    <h2>Login</h2>
    <form @submit.prevent="doLogin">
      <input v-model="email" type="email" placeholder="Email" required />
      <input v-model="password" type="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>

    <p v-if="error" class="error">{{ error }}</p>
    <router-link to="/register">Register</router-link>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const email = ref('');
const password = ref('');
const error = ref(null);
const router = useRouter();
const auth = useAuthStore();

async function doLogin() {
  error.value = null;
  try {
    await auth.login(email.value, password.value);

    if (auth.isAdmin) router.push('/admin');
    else router.push('/dashboard');
  } catch (e) {
    console.error(e);
    error.value = e.response?.data?.message || e.message || 'Login failed';
  }
}
</script>

<style scoped>
.error { color: red; }
.auth-page { max-width: 360px; margin: auto; padding-top: 60px; display:flex; flex-direction:column; gap:10px; }
</style>
