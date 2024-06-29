import { defineStore } from "pinia";
import axios from "axios";
import { useGeneralStore } from "@/stores/general-store";
import { ref } from "vue";
import OpenAPIClientAxios from "openapi-client-axios";

export const useAuthStore = defineStore("auth", () => {
	const genStore = useGeneralStore();
	// const api = new OpenAPIClientAxios({ definition: genStore.api.openApiUrl });
	// api.init();

	const loggedIn = ref(false);

	async function getLoggedIn() {
		// const token = localStorage.getItem("authToken");
		// const res = await axios.post("/1/Account/CheckToken", { token });

		// const client = await api.getClient();
		// const res = await client.checkToken();

		// loggedIn.value = res.status === 200;
		loggedIn.value = true;
	}

	return { loggedIn, getLoggedIn };
});
