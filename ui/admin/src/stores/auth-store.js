import { ref } from "vue";
import { defineStore } from "pinia";
import { useApi } from '@/composables/useApi.js';
import { useGeneralStore } from '@/stores/general-store.js';

export const useAuthStore = defineStore("auth", () => {
	const loggedIn = ref(false);
	const generalStore = useGeneralStore();

	async function getLoggedIn() {
		try {
			const res = await useApi().get("/1.1/Account/CheckSession");

			if (res.status === 200) {
				loggedIn.value = true;

				return;
			}

			if (generalStore.environment === "development") {
				console.log(res);
			}
		} catch (error) {
			if (generalStore.environment === "development") {
				console.log(error);
			}
		}

		loggedIn.value = false;

		return;
	}

	async function logOut() {
		try {
			const res = await useApi().post('/1.1/Account/Logout');

			if (res.status === 200) {
				loggedIn.value = false;

				return;
			}
		} catch (error) {
			if (generalStore.environment === "development") {
				console.log(error);
			}
		}

		return;
	}

	return { loggedIn, getLoggedIn, logOut };
});
