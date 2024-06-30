import { ref } from 'vue';
import { defineStore } from 'pinia';
import { useApi } from '@/composables/useApi.js';

export const useRoleStore = defineStore('role', () => {
	const roles = ref([]);

	async function getRoles() {
		try {
			useApi().get('/1.1/Roles').then(res => {
				if (res.status === 200) {
					roles.value = res.data;

					return;
				}
			});
		} catch (error) {
			console.error(error);
		}

		return;
	}

	async function userInRole(roleName) {
		try {
			useApi().get('/1.1/Roles/UserInRole', { name: roleName }).then(res => {
				if (res.status === 200) {
					return res.data;
				}
			});
		} catch (error) {
			console.error(error);
		}

		return false;
	}

	return { roles, getRoles, userInRole };
});
