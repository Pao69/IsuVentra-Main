import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import api from "@/services/api";

// Pages
import LoginPage from "@/views/LoginPage.vue";
import RegisterPage from "@/views/RegisterPage.vue";
import UserDashboard from "@/views/UserDashboard.vue";
import JoinPage from "@/views/JoinPage.vue";
import AdminDashboard from "@/views/admin/AdminDashboard.vue";

const routes = [
  { path: "/", name: "login", component: LoginPage },
  { path: "/register", name: "register", component: RegisterPage },
  {
    path: "/dashboard",
    name: "dashboard",
    component: UserDashboard,
    meta: { requiresAuth: true, role: "user" },
  },
  {
    path: "/join",
    name: "join",
    component: JoinPage,
    meta: { requiresAuth: true, role: "user" },
  },
  {
    path: "/admin",
    name: "admin",
    component: AdminDashboard,
    meta: { requiresAuth: true, role: "admin" },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// 🔐 Global Route Guard
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();

  // Always allow login/register if not authenticated
  if (!auth.token && ["login", "register"].includes(to.name)) {
    return next();
  }

  // Prevent logged-in users from accessing login/register
  if (auth.token && ["login", "register"].includes(to.name)) {
    return auth.isAdmin ? next({ name: "admin", replace: true }) : next({ name: "dashboard", replace: true });
  }

  // Routes that require authentication
  if (to.meta.requiresAuth) {
    if (!auth.token) return next({ name: "login", replace: true });

    try {
      // Check token validity via API and update user data
      const response = await api.get("/validate-token"); // throws 401 if invalid
      if (response.data.user) {
        auth.setAuth(response.data.user, auth.token);
      }
    } catch (err) {
      auth.logout(); // clear token & user data
      return next({ name: "login", replace: true });
    }

    // Role-based protection
    if (to.meta.role && to.meta.role !== auth.role) {
      return auth.isAdmin ? next({ name: "admin", replace: true }) : next({ name: "dashboard", replace: true });
    }
  }

  next();
});

export default router;
