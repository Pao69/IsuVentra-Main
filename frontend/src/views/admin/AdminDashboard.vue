<template>
  <div class="admin-container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>Admin Panel</h2>

      <nav>
        <button @click="currentPage = 'overview'">Overview</button>
        <button @click="currentPage = 'students'">Students</button>
        <button @click="currentPage = 'events'">Events</button>
        <button @click="currentPage = 'participation'">Participation</button>

        <button class="logout" @click="logout">
          Logout
        </button>
      </nav>
    </aside>

    <!-- Main content -->
    <main class="content">
      <!-- Overview / Dashboard -->
      <section v-if="currentPage === 'overview'">
        <h1>Dashboard Overview</h1>

        <div class="grid">
          <div class="card">
            <h2>Total Students</h2>
            <p>{{ stats.students }}</p>
          </div>

          <div class="card">
            <h2>Total Events</h2>
            <p>{{ stats.events }}</p>
          </div>

          <div class="card">
            <h2>Participation Count</h2>
            <p>{{ stats.participation }}</p>
          </div>
        </div>

        <div class="chart-container">
          <h3>Participation Chart</h3>
          <canvas ref="chartCanvas"></canvas>
        </div>

        <div class="widgets">
          <div class="widget">
            <h3>Ongoing Events</h3>
            <ul>
              <li v-for="e in ongoingEvents" :key="e.id">
                {{ e.name }} ({{ e.location }})
              </li>
            </ul>
          </div>

          <div class="widget">
            <h3>Latest Participation</h3>
            <ul>
              <li v-for="p in latestParticipation" :key="p.id">
                {{ p.student_name }} → {{ p.event_name }}
              </li>
            </ul>
          </div>
        </div>
      </section>

      <!-- Students Page -->
      <section v-if="currentPage === 'students'">
        <h1>Students</h1>

        <button @click="showAddStudent = true">Add Student</button>

        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Is Admin</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="s in students" :key="s.id">
              <td>{{ s.name }}</td>
              <td>{{ s.email }}</td>
              <td>{{ s.is_admin }}</td>
              <td>
                <button @click="editStudent(s)">Edit</button>
                <button @click="deleteStudent(s.id)">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- Events Page -->
      <section v-if="currentPage === 'events'">
        <h1>Events</h1>

        <button @click="showAddEvent = true">Add Event</button>

        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Location</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="e in events" :key="e.id">
              <td>{{ e.name }}</td>
              <td>{{ e.location }}</td>
              <td>{{ e.is_ongoing ? "Ongoing" : "Closed" }}</td>
              <td>
                <button @click="editEvent(e)">Edit</button>
                <button @click="deleteEvent(e.id)">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- Participation Page -->
      <section v-if="currentPage === 'participation'">
        <h1>Participation Records</h1>

        <table>
          <thead>
            <tr>
              <th>Student</th>
              <th>Event</th>
              <th>Time-in</th>
              <th>Time-out</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in participation" :key="p.id">
              <td>{{ p.student_name }}</td>
              <td>{{ p.event_name }}</td>
              <td>{{ p.time_in }}</td>
              <td>{{ p.time_out }}</td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "../../stores/auth";
import api from "@/services/api"; 
import Chart from "chart.js/auto";

const auth = useAuthStore();

const currentPage = ref("overview");
const students = ref([]);
const events = ref([]);
const participation = ref([]);

const stats = ref({
  students: 0,
  events: 0,
  participation: 0,
});

const ongoingEvents = ref([]);
const latestParticipation = ref([]);

const chartCanvas = ref(null);
let chartInstance;

// Load all data
async function loadAdminData() {
  const [stuRes, eveRes, partRes] = await Promise.all([
    api.get("/students"),
    api.get("/events"),
    api.get("/participation"),
  ]);

  students.value = stuRes.data;
  events.value = eveRes.data;
  participation.value = partRes.data;

  stats.value = {
    students: students.value.length,
    events: events.value.length,
    participation: participation.value.length,
  };

  ongoingEvents.value = events.value.filter((e) => e.is_ongoing);
  latestParticipation.value = participation.value.slice(0, 5);

  renderChart();
}

function renderChart() {
  if (!chartCanvas.value) return;

  if (chartInstance) chartInstance.destroy();

  const counts = events.value.map((e) => {
    return participation.value.filter((p) => p.event_id === e.id).length;
  });

  chartInstance = new Chart(chartCanvas.value, {
    type: "bar",
    data: {
      labels: events.value.map((e) => e.name),
      datasets: [
        {
          label: "Participation Count",
          data: counts,
        },
      ],
    },
  });
}

function logout() {
  auth.logout();
}

onMounted(() => {
  loadAdminData();
});
</script>

<style scoped>
.admin-container {
  display: flex;
  height: 100vh;
}

.sidebar {
  width: 220px;
  background: #222;
  padding: 20px;
  color: white;
}

.sidebar button {
  width: 100%;
  margin-bottom: 10px;
}

.content {
  flex: 1;
  padding: 20px;
}

.grid {
  display: flex;
  gap: 20px;
  margin-bottom: 30px;
}

.card {
  background: #f7f7f7;
  padding: 20px;
  border-radius: 8px;
  flex: 1;
}

.chart-container {
  max-width: 600px;
  margin-bottom: 30px;
}

.widget {
  background: #fff;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
}
</style>
