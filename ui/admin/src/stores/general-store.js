import { defineStore } from "pinia";
import { useApi } from "@/composables/useApi.js";

export const useGeneralStore = defineStore("general", {
	state: () => ({
		title: "",
		theme: "",
		themeUpdated: false,
		darkMode: null,
		darkTheme: "",
		lightTheme: "",
		ripple: false,
		menuMode: "static",
		inputStyle: "outlined",
		environment: "development",
		api: {
			useApi: false,
			baseUrl: null,
			headers: {},
			openApiUrl: null,
			fetchColumns: null,
			fetchUsers: null,
			fetchCurrentUser: null,
		},
		displayNames: {
			title: "",
			header: "",
		},
		currentUser: {
			userId: 0,
		},
		menu: [],
	}),
	actions: {
		refreshUserId() {
			const api = useApi();
			this.currentUser.userId = 0;

			api.get("/1.1/Account").then((res) => {
				if (res.status === 200) {
					this.currentUser.userId = res.data.id;
				}

				return;
			});

			return;
		},
	},
});
