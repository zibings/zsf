import type { Router } from "vue-router";
import { useUserStore } from "stores/user";

export default function useAuthGuard(router: Router): void {
	const userStore = useUserStore();

	router.beforeEach((to, from, next) => {
		if (to.meta.requiresAuth === undefined) {
			next();

			return;
		}

		if (to.meta.requiresAuth && !userStore.loggedIn) {
			next({ name: "login" });

			return;
		}

		if (!to.meta.requiresAuth && userStore.loggedIn) {
			next({ name: "profile" });

			return;
		}

		next();

		return;
	});
}
