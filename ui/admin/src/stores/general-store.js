import { defineStore } from "pinia";
// import Cookies from "js-cookie";

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
			baseUrl: null,
			headers: {},
			openApiUrl: null,
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
	actions: {},
});
