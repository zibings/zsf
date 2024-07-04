import { ref } from 'vue';
import { defineStore } from 'pinia';
import { useApi } from 'composables/useApi';
import { useGeneralStore } from 'stores/general';

interface Role {
	created: string;
	id: number;
	name: string;
}

export const useRoleStore = defineStore("auth", () => {
	const api = useApi();
	const roles = ref<Record<string, Role>>({ });
	const generalStore = useGeneralStore();

	async function getUserRoles(): Promise<Record<string, Role>> {
		try {
			const roleRes = await api.get<Record<string, Role>>("/1.1/Roles/UserRoles");

			if (roleRes.status === 200) {
				roles.value = roleRes.data;
			}
		} catch (error) {
			if (generalStore.environment === 'development') {
				console.error(error);
			}

			roles.value = {};
		}

		return roles.value;
	}

	return { getUserRoles };
});
