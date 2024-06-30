import AppLayout from "@/layout/AppLayout.vue";
import { createRouter, createWebHistory } from "vue-router";
import { useGeneralStore } from "@/stores/general-store";
import { useAuthStore } from "@/stores/auth-store";

const routes = [
	{
		path: "/",
		component: AppLayout,
		children: [
			{
				path: "/",
				name: "dashboard",
				component: () => import("@/views/Dashboard.vue"),
			},
			{
				path: "/users",
				name: "users",
				component: () => import("@/views/UserManagement.vue"),
			},
			{
				path: "/users/:id",
				name: "userEdit",
				component: () => import("@/views/UserEdit.vue"),
			},
			{
				path: "/uikit/charts",
				name: "charts",
				component: () => import("@/views/uikit/Chart.vue"),
			},
			{
				path: "/pages/empty",
				name: "empty",
				component: () => import("@/views/pages/Empty.vue"),
			},
		],
	},
	{
		path: "/landing",
		name: "landing",
		meta: { noAuth: true },
		component: () => import("@/views/pages/Landing.vue"),
	},
	{
		path: "/pages/notfound",
		name: "notfound",
		meta: { noAuth: true },
		component: () => import("@/views/pages/NotFound.vue"),
	},

	{
		path: "/auth/login",
		name: "login",
		meta: { noAuth: true },
		component: () => import("@/views/pages/auth/Login.vue"),
	},
	{
		path: "/auth/forgotpass",
		name: "forgotpass",
		meta: { noAuth: true },
		component: () => import("@/views/pages/auth/ForgotPassword.vue"),
	},
	{
		path: "/auth/access",
		name: "accessDenied",
		meta: { noAuth: true },
		component: () => import("@/views/pages/auth/Access.vue"),
	},
	{
		path: "/auth/error",
		name: "error",
		meta: { noAuth: true },
		component: () => import("@/views/pages/auth/Error.vue"),
	},
];

const router = createRouter({
	history: createWebHistory(),
	routes,
	scrollBehavior() {
		// always scroll to top
		return { top: 0 };
	},
});

router.beforeEach(async (to, from, next) => {
	const authStore = useAuthStore();
	const genStore = useGeneralStore();

	if (!authStore.loggedIn) {
		await authStore.getLoggedIn();
	}

	if (!to.meta.noAuth && !authStore.loggedIn) {
		next({ name: "login" });

		return;
	}

	if ((to.name === "login" || to.name === "forgotpass") && authStore.loggedIn) {
		next({ name: "dashboard" });

		return;
	}

	next();

	return;
});

export default router;
