<template>
	<nav class="navbar-container">
		<RouterLink to="/" class="home-link">
			<div class="navbar-logo"></div>
			ZSF Website
		</RouterLink>

		<Menubar :model="items" />
	</nav>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Menubar from 'primevue/menubar';
import { useAuthStore } from 'stores/auth';
import type { MenuItem } from 'primevue/menuitem';

const router = useRouter();
const authStore = useAuthStore();

const items = ref<MenuItem[]>([]);

if (authStore.isLoggedIn) {
	items.value = [
		{
			label: 'About',
			icon: 'pi pi-home',
			route: '/'
		},
		{
			label: 'Features',
			icon: 'pi pi-star',
			command: () => router.push({ name: 'home' })
		},
		{
			label: 'Contact',
			icon: 'pi pi-envelope',
			url: 'https://vuejs.org',
			target: '_blank'
		}
	];
} else {
	items.value = [
		{
			label: 'Login',
			icon: 'pi pi-lock',
			route: '/login'
		},
		{
			label: 'Register',
			icon: 'pi pi-user',
			route: { name: 'register' }
		}
	];
}
</script>

<style scoped>
nav.navbar-container {
	padding: 10px;
	display: flex;
	justify-content: space-between;
}
</style>
