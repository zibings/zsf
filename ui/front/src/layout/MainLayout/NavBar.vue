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
import type { MenuItem } from 'primevue/menuitem';

const router = useRouter();

const items = ref<MenuItem[]>([
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
]);
</script>

<style scoped>
nav.navbar-container {
	padding: 10px;
	display: flex;
	justify-content: space-between;
}
</style>
