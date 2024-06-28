<script setup lang="ts">
import {RouterLink} from "vue-router";
import {useAuthStore} from "@/stores/auth";
import {storeToRefs} from "pinia";

const authStore = storeToRefs(useAuthStore());

console.log(authStore);
</script>

<template>
	<nav>
		<RouterLink to="home">Home</RouterLink>
		<RouterLink to="login">Login</RouterLink>
		<RouterLink to="register">Register</RouterLink>
		<RouterLink to="profile" v-if="authStore.isLoggedIn.value">Profile</RouterLink>
	</nav>
</template>

<style scoped>
nav {
	font-size: 12px;
	display: flex;
	place-items: center;
	margin-top: 100px;
}

nav a.router-link-exact-active {
	color: var(--color-text);
}

nav a.router-link-exact-active:hover {
	background-color: transparent;
}

nav a {
	display: inline-block;
	padding: 0 1rem;
	border-left: 1px solid var(--color-border);
}

nav a:first-of-type {
	border: 0;
}

@media (min-width: 1024px) {
	nav {
		text-align: left;
		font-size: 1rem;
		padding: 1rem 0;
	}
}
</style>