import { ref } from "vue";
import { defineStore } from "pinia";
import { useApi } from "@/composables/useApi.js";

export const useRoleStore = defineStore("role", () => {
	const roles = ref([]);
	const api = useApi();

	async function getRoles() {
		try {
			const roles = await api.get("/1.1/Roles");

			if (roles.status === 200) {
				roles.value = roles.data;

				return;
			}
		} catch (error) {
			console.error(error);
		}

		return;
	}

	async function userInRole(roleName) {
		try {
			const res = await api.get("/1.1/Roles/UserInRole", { name: roleName });

			if (res.status === 200) {
				return res.data;
			}
		} catch (error) {
			console.error(error);
		}

		return false;
	}

	async function getUserRoles() {
		try {
			const roles = await api.get("/1.1/Roles/UserRoles");

			if (roles.status === 200) {
				return roles.data;
			}
		} catch (error) {
			console.error(error);
		}

		return [];
	}

	return { roles, getRoles, userInRole, getUserRoles };
});
