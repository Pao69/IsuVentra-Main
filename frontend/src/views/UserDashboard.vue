<template>
  <div>
    <h1>Events</h1>

    <button @click="$router.push('/join')">Join Event (Scan QR)</button>

    <div v-if="loading">Loading events...</div>

    <ul v-if="events.length">
      <li v-for="e in events" :key="e.id">
        <strong>{{ e.name }}</strong><br>
        <span>{{ e.description }}</span>
      </li>
    </ul>

    <p v-if="!loading && events.length === 0">No events available.</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api"; 

const events = ref([]);
const loading = ref(true);

async function fetchEvents() {
  try {
    const res = await api.get("/events"); // backend route
    events.value = res.data.events ?? res.data;
  } catch (err) {
    console.error("Error fetching events:", err);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchEvents();
});
</script>
