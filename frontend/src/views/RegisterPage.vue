<template>
  <div class="auth-page">
    <h2>Register</h2>
    <form @submit.prevent="doRegister">
      <input v-model="name" type="text" placeholder="Full name" required />
      <input v-model="email" type="email" placeholder="Email" required />
      <input v-model="password" type="password" placeholder="Password" required />
      <input v-model="passwordConfirmation" type="password" placeholder="Confirm password" required />
      <button type="submit">Register</button>
    </form>

    <p v-if="error" class="error">{{ error }}</p>
    <p v-if="success" class="success">{{ success }} — redirecting to login...</p>

    <router-link to="/">Already have an account? Login</router-link>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from "@/services/api"; 
import { useRouter } from 'vue-router';

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const error = ref(null);
const success = ref(null);
const router = useRouter();

async function doRegister() {
  error.value = null;
  success.value = null;

  if (password.value !== passwordConfirmation.value) {
    error.value = 'Passwords do not match';
    return;
  }

  try {
    // backend endpoint for register
    await api.post('/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    });

    success.value = 'Registration successful';
    // backend won't issue token, so redirect user to login
    setTimeout(() => {
      router.push('/login');
    }, 1000);
  } catch (e) {
    console.error(e);
    error.value = e.response?.data?.message || 'Registration failed';
  }
}
</script>

<style scoped>
.error { color: red; }
.success { color: green; }
.auth-page { max-width: 360px; margin: auto; padding-top: 60px; display:flex; flex-direction:column; gap:10px; }
</style>
