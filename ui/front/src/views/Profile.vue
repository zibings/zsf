<template>
	<h1>Profile</h1>

	<div>
		<a class="link" @click="doLogOut">Log Out</a>
	</div>
</template>

<script setup>
import { useRouter } from "vue-router";
import { useUserStore } from "stores/user";

const router = useRouter();
const userStore = useUserStore();

const doLogOut = () => {
	userStore.logOut();

	router.push({ name: "login" });

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
