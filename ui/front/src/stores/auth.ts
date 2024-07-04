import { defineStore } from "pinia";
import { useApi } from 'composables/useApi';
import { useGeneralStore } from 'stores/general';

export const useAuthStore = defineStore("auth", {
	state: () => ({
		isLoggedIn: false,
	}),
	actions: {
		async getLoggedIn() {
			const api = useApi();
			const generalStore = useGeneralStore();

			try {
				const res = await api.get("/1.1/Account");

				if (res.status === 200) {
					this.isLoggedIn = true;

					return;
				}
			} catch (error) {
				if (generalStore.environment === 'development') {
					console.log(error);
				}
			}

			this.logOut();

			return;
		},
		async logOut() {
			const api = useApi();
			const generalStore = useGeneralStore();

			try {
				const res = await api.post('/1.1/Account/Logout');

				if (res.status === 200) {
					this.isLoggedIn = false;
					generalStore.currentUser.userId = 0;

					return;
				}
			} catch (error) {
				if (generalStore.environment === 'development') {
					console.log(error);
				}
			}

			return;
		},
	}
});
