import { ref } from "vue";
import { defineStore } from "pinia";
import { useApi } from '@/composables/useApi.js';

export const useAuthStore = defineStore("auth", () => {
	const loggedIn = ref(false);

	async function getLoggedIn() {
		try {
			useApi().get("/1.1/Account/CheckSession").then(res => {
				if (res.status === 200) {
					loggedIn.value = true;

					return;
				}

				localStorage.removeItem("authToken");

				return;
			});
		} catch (error) {
			loggedIn.value = false;
			localStorage.removeItem("authToken");
		}

		return;
	}

	return { loggedIn, getLoggedIn };
});
