import { defineStore } from 'pinia';

export const useGeneralStore = defineStore("general", {
	state: () => ({
		api: {
			useApi: false,
			baseUrl: "/"
		},
		currentUser: {
			userId: 0,
		},
		environment: "development",
	}),
	actions: {}
});
