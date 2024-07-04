import { createRouter, createWebHistory, createMemoryHistory } from "vue-router";
import { useAuthStore } from "stores/auth";
import routes from "./routes";

const router = createRouter({
	history: import.meta.env.SSR ? createMemoryHistory(import.meta.env.BASE_URL) : createWebHistory(import.meta.env.BASE_URL),
	routes,
});

export default router;

router.beforeEach(async (to, from, next) => {
	const authStore = useAuthStore();

	await authStore.getLoggedIn();

	if (to.meta.requiresAuth && !authStore.isLoggedIn) {
		next({ name: "login" });

		return;
	}

	if (authStore.isLoggedIn && !to.meta.requiresAuth) {
		next({ name: 'profile' });

		return;
	}

	next();

	return;
});
