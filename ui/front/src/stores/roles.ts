import { ref } from 'vue';
import { defineStore } from 'pinia';
import { useApi } from 'composables/useApi';
import { useGeneralStore } from 'stores/general';

export const useRoleStore = defineStore("auth", () => {
	const api = useApi();

	async function getUserRoles():
});
