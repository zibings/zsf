import {defineStore} from "pinia";

export const useAuthStore = defineStore("auth", {
	state: () => ({
		apiToken: '',
		isLoggedIn: false,
	}),
	actions: {
		setApiToken(token: string) {
			this.apiToken = token;
			this.isLoggedIn = true;

			return;
		},
		clearApiToken() {
			this.apiToken = '';
			this.isLoggedIn = false;

			return;
		},
	}
});
