import { defineStore } from 'pinia';

export const useUserStore = defineStore("user", {
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
		}
	},
	getters: {
		isLoggedIn: (state) => state.loggedIn,
		getUserId: (state) => state.userId,
	},
	state: () => ({
		loggedIn: false,
		userId: 0,
	}),
});
