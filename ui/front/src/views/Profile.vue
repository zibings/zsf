<template>
	<h1>Profile</h1>

	<div>
		<a href="#" @click="doLogOut">Log Out</a>
	</div>
</template>

<script setup>
import router from "@/router";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();

const doLogOut = () => {
	authStore.logOut();

	router.push("/login");

	return;
};
</script>

<style scoped>
div {
	display: flex;
	justify-content: center;
	align-items: center;
}
</style>
