import {useAuthStore} from "@/stores/auth";
import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes: [
		{
			path: '/',
			name: 'home',
			component: () => import('../views/HomeView.vue')
		},
		{
			path: '/login',
			name: 'login',
			component: () => import('../views/Login.vue')
		},
		{
			path: '/register',
			name: 'register',
			component: () => import('../views/Register.vue')
		},
		{
			path: '/profile',
			name: 'profile',
			meta: { requiresAuth: true },
			component: () => import('../views/Profile.vue')
		}
	]
});

router.beforeEach(async (to, from, next) => {
	const authStore = useAuthStore();

	if (to.meta.requiresAuth && !authStore.isLoggedIn) {
		next({ name: "login" });

		return;
	}

	next();

	return;
});

export default router;
