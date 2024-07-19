import { defineStore } from "pinia";

export const useUserStore = defineStore("user", {
	state: () => ({
		loggedIn: false,
		userId: 0,
	}),

	actions: {
		logIn(userId: number) {
			this.loggedIn = true;
			this.userId = userId;

			return;
		},
		logOut() {
			this.loggedIn = false;
			this.userId = 0;

			return;
		},
	},
});
