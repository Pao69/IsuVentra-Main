<template>
  <div id="app">
    <nav v-if="auth.isLoggedIn">
      <router-link to="/dashboard" v-if="auth.role === 'user'">Dashboard</router-link>
      <router-link to="/join" v-if="auth.role === 'user'">Join Event</router-link>
      <router-link to="/admin" v-if="auth.role === 'admin'">Admin Dashboard</router-link>
      <button @click="logout">Logout</button>
    </nav>
    <nav v-else>
      <router-link to="/">Login</router-link>
      <router-link to="/register">Register</router-link>
    </nav>

    <router-view />
  </div>
</template>

<script setup>
import { useAuthStore } from "@/stores/auth";
import { onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "@/services/api";

const auth = useAuthStore();
const router = useRouter();

const logout = () => {
  auth.logout();
  router.push({ name: "login" });
};

onMounted(async () => {
  const auth = useAuthStore();
  if (auth.token) {
    try {
      const response = await api.get('/validate-token');
      // Update user data from server if needed
      if (response.data.user) {
        auth.setAuth(response.data.user, auth.token);
      }
    } catch {
      auth.logout();
    }
  }
});
</script>



<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

nav {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 15px; /* spacing between items */
  padding: 15px;
  margin-bottom: 20px;
  background-color: #f5f5f5;
  border-bottom: 1px solid #ddd;
}

nav a {
  font-weight: bold;
  color: #2c3e50;
  text-decoration: none;
  position: relative;
  padding: 0 10px;
}

/* separator using pseudo-element */
nav a + a::before {
  content: "|";
  position: absolute;
  left: 0;
  color: #ccc;
}

/* active route highlight */
nav a.router-link-exact-active {
  color: #42b983;
}

nav button {
  cursor: pointer;
  font-weight: bold;
  background: #e74c3c;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 3px;
}

nav button:hover {
  background: #c0392b;
}
</style>
