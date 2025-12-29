import type { RouteLocationNormalizedGeneric, Router, NavigationGuardNext } from 'vue-router';

type BeforeEachCallable = (to: RouteLocationNormalizedGeneric, from : RouteLocationNormalizedGeneric, next: NavigationGuardNext) => boolean;

export default function useAuthGuard(
	router: Router,
	beforeEach: BeforeEachCallable
): void {
	router.beforeEach((to, from, next) => {
		if (to.meta.requiresAuth === undefined) {
			next();

			return;
		}

		if (beforeEach(to, from, next)) {
			return;
		}

		next();

		return;
	});

	return;
}
