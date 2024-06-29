import {defineStore} from "pinia";

export const useAuthStore = defineStore("auth", {
	state: () => ({
		apiToken: '',
		isLoggedIn: false,
	}),
	actions: {
		logIn(email: string, password: string) {
			// TODO: Call API here, for reasons
			this.setApiToken('token');

			return;
		},
		logOut() {
			this.clearApiToken();

			return;
		},
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
