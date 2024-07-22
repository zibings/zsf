import "./assets/main.css";

import { ViteSSG } from "vite-ssg";
import { createPinia } from "pinia";
import { createApi } from "composables/useApi";
import { useGeneralStore } from "stores/general";
import { useConfig } from "composables/useConfig";
import useAuthGuard from "composables/useAuthGuard";

import App from "./App.vue";
import router from "./router";
import PrimeVue from "primevue/config";
import Aura from "@primevue/themes/aura";

import Ripple from "primevue/ripple";
import Tooltip from "primevue/tooltip";
import StyleClass from "primevue/styleclass";
import BadgeDirective from "primevue/badgedirective";

import ToastService from "primevue/toastservice";
import DialogService from "primevue/dialogservice";
import ConfirmationService from "primevue/confirmationservice";

const routes = router.getRoutes();

const scrollBehavior = (to, from, savedPosition) => {
	if (to.hash) {
		return {
			el: to.hash,
			top: 0,
			behavior: "smooth",
		};
	}

	return new Promise((resolve) => {
		setTimeout(() => {
			if (savedPosition) {
				return savedPosition;
			} else {
				resolve({ left: 0, top: 0 });
			}
		}, 20);
	});
};

export const createApp = ViteSSG(
	App,
	{
		base: import.meta.env.BASE_URL,
		routes,
		scrollBehavior,
	},
	({ app, router, initialState }) => {
		fetch(`${import.meta.env.BASE_URL}config.json`)
	  .then((res) => res.json())
	  .then(async (config) => {
		  const pinia = createPinia();
		  app.use(pinia);

		  const conf = useConfig(config);
		  useGeneralStore().$patch(conf);

		  app.use(PrimeVue, {
			  theme: {
				  preset: Aura,
			  },
		  });

		  app.use(ConfirmationService);
		  app.use(ToastService);
		  app.use(DialogService);
		  app.directive("tooltip", Tooltip);
		  app.directive("ripple", Ripple);
		  app.directive("badge", BadgeDirective);
		  app.directive("styleclass", StyleClass);

		  const api = createApi(conf.api.baseUrl);
		  app.provide("$api", api);

		  if (import.meta.env.SSR) {
			  // this will be stringified and set to window.__INITIAL_STATE__
			  initialState.pinia = pinia.state.value;
		  } else {
			  // on the client side, we restore the state
			  pinia.state.value = initialState?.pinia || {};
		  }

		  router.beforeEach((to, from, next) => {
			  const generalStore = useGeneralStore(pinia);
			  generalStore.initialize();

			  next();
		  });

		  useAuthGuard(router);
	  });
	},
);

// fetch(`${import.meta.env.BASE_URL}config.json`)
// 	.then((res) => res.json())
// 	.then(async (config) => {
// 		const conf = useConfig(config);
// 		await generalStore.$patch(conf);

// 		const app = createApp(App);

// 		app.use(pinia);
// 		app.use(PrimeVue, {
// 			theme: {
// 				preset: Aura,
// 			},
// 		});

// 		useAuthGuard(router);

// 		// app.use(ConfirmationService);
// 		// app.use(ToastService);
// 		// app.use(DialogService);
// 		app.use(router);

// 		app.config.globalProperties.$api = createApi(app.config.globalProperties.$conf.api.baseUrl);
// 		app.provide("$api", app.config.globalProperties.$api);

// 		// app.directive("tooltip", Tooltip);
// 		// app.directive("ripple", Ripple);
// 		// app.directive("badge", BadgeDirective);
// 		// app.directive("styleclass", StyleClass);

// 		app.mount("#app");

// 		return;
// 	});
