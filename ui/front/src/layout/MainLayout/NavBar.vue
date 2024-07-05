<template>
	<nav class="navbar-container">
		<RouterLink to="/" class="home-link">
			<div class="navbar-logo"></div>
			ZSF Website
		</RouterLink>

		<Menubar :model="authedItems" v-if="userStore.isLoggedIn" />
		<Menubar :model="publicItems" v-else />
	</nav>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Menubar from 'primevue/menubar';
import { useUserStore } from 'stores/user';
import type { MenuItem } from 'primevue/menuitem';

const router = useRouter();
const userStore = useUserStore();

const publicItems = ref<MenuItem[]>([
	{
		label: 'Login',
		icon: 'pi pi-lock',
		command: () => router.push({ name: 'login' })
	},
	{
		label: 'Register',
		icon: 'pi pi-user',
		command: () => router.push({ name: 'register' })
	}
]);

const authedItems = ref<MenuItem[]>([
	{
		label: 'About',
		command: () => router.push({ name: 'home' })
	},
	{
		label: 'Features',
		command: () => router.push({ name: 'home' })
	},
	{
		label: 'Profile',
		command: () => router.push({ name: 'profile' })
	},
	{
		label: 'Contact',
		url: 'https://vuejs.org',
		target: '_blank'
	},
	{
		label: 'Log Out',
		command: () => {
			userStore.logOut();
			router.push({ name: 'login' });
		}
	}
]);
</script>

<style scoped>
nav.navbar-container {
	padding: 10px;
	display: flex;
	justify-content: space-between;
}
</style>
