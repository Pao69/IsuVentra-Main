import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

// Pages
import LoginPage from "../views/LoginPage.vue";
import RegisterPage from "../views/RegisterPage.vue";
import UserDashboard from "../views/UserDashboard.vue";
import JoinPage from "../views/JoinPage.vue";
import AdminDashboard from "../views/admin/AdminDashboard.vue";

const routes = [
  { path: "/", name: "login", component: LoginPage },
  { path: "/register", name: "register", component: RegisterPage },

  // Normal user dashboard
  {
    path: "/dashboard",
    name: "dashboard",
    component: UserDashboard,
    meta: { requiresAuth: true, role: "user" },
  },

  // Join event page
  {
    path: "/join",
    name: "join",
    component: JoinPage,
    meta: { requiresAuth: true, role: "user" },
  },

  // Admin dashboard
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
router.beforeEach((to, from, next) => {
  const auth = useAuthStore();

  // Route requires authentication
  if (to.meta.requiresAuth) {
    if (!auth.token) {
      if (from.name !== "login") {
        return next({ name: "login", replace: true });
      }
      return next();
    }

    // Role-based protection
    if (to.meta.role && to.meta.role !== auth.role) {
      if (auth.role === "admin" && to.name !== "admin") {
        return next({ name: "admin", replace: true });
      } else if (auth.role === "user" && !["dashboard", "join"].includes(to.name)) {
        return next({ name: "dashboard", replace: true });
      }
    }
  } else {
    // Prevent logged-in users from visiting login/register
    if (auth.token && ["login", "register"].includes(to.name)) {
      if (auth.role === "admin") return next({ name: "admin", replace: true });
      return next({ name: "dashboard", replace: true });
    }
  }

  next();
});

export default router;
