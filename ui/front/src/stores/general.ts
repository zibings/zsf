import { defineStore } from 'pinia';

export const useGeneralStore = defineStore("general", {
	state: () => ({
		environment: "development",
		api: {
			useApi: false,
			baseUrl: "/"
		}
	}),
	actions: {}
});
