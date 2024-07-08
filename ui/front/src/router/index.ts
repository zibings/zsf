import routes from "./routes";
import { createRouter, createWebHistory, createMemoryHistory } from 'vue-router';

const router = createRouter({
	history: import.meta.env.SSR ? createMemoryHistory(import.meta.env.BASE_URL) : createWebHistory(import.meta.env.BASE_URL),
	routes,
});

export default router;
