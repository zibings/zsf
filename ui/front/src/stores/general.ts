import { defineStore } from "pinia";

export const useGeneralStore = defineStore("general", {
	state: () => ({
		isReady: false,
		api: {
			useApi: false,
			baseUrl: "/",
		},
		environment: "development",
	}),

	actions: {
		initialize() {
			if (this.isReady) {
				return;
			}

			this.isReady = true;
			this.environment = import.meta.env.MODE ?? "development";
			this.api.useApi = import.meta.env.VITE_API_USE_API === "true";
			this.api.baseUrl = import.meta.env.VITE_API_BASE_URL ?? "/";
		},
	},
});
