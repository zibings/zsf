import { ref } from "vue";
import { defineStore } from "pinia";
import { useApi } from '@/composables/useApi.js';
import { useGeneralStore } from '@/stores/general-store.js';

export const useAuthStore = defineStore("auth", () => {
	const loggedIn = ref(false);
	const generalStore = useGeneralStore();

	async function getLoggedIn() {
		try {
			useApi().get("/1.1/Account/CheckSession").then(res => {
				if (res.status === 200) {
					loggedIn.value = true;

					return;
				}

				if (generalStore.environment === "development") {
					document.cookie = "zsf_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
				}

				return;
			});
		} catch (error) {
			loggedIn.value = false;

			if (generalStore.environment === "development") {
				document.cookie = "zsf_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
			}
		}

		return;
	}

	return { loggedIn, getLoggedIn };
});
