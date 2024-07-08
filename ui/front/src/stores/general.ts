import { defineStore } from 'pinia';

export const useGeneralStore = defineStore("general", {
	state: () => ({
		api: {
			useApi: false,
			baseUrl: "/",
		},
		environment: "development",
	}),
});
