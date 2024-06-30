import { ref } from "vue";
import { defineStore } from "pinia";
import { useApi } from '@/composables/useApi.js';

export const useAuthStore = defineStore("auth", () => {
	const loggedIn = ref(false);

	async function getLoggedIn() {
		const token = localStorage.getItem("authToken");
		useApi().post("/1.1/Account/CheckToken", { token }).then(res => {
			if (res.status === 200) {
				loggedIn.value = true;

				return;
			}

			localStorage.removeItem("authToken");

			return;
		});

		return;
	}

	return { loggedIn, getLoggedIn };
});
