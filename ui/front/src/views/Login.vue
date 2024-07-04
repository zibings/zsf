<template>
	<h1>Log In</h1>

	<form>
		<FloatLabel>
			<InputText v-model="email" type="text" id="login-email" label="Email" />
			<label for="login-email">Email Address</label>
		</FloatLabel>

		<FloatLabel>
			<InputText v-model="password" type="password" id="login-password" label="Password" />
			<label for="login-password">Password</label>
		</FloatLabel>

		<Button label="Log In" @click="doLogIn" />
	</form>
</template>

<script setup>
import { ref } from "vue";
import router from "@/router";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import { useAuthStore } from "@/stores/auth";
import FloatLabel from "primevue/floatlabel";
import { useApi } from 'composables/useApi.js';

const api = useApi();

const email = ref("");
const password = ref("");
const authStore = useAuthStore();

const doLogIn = () => {
	//router.push("/profile");
	api.post("/1.1/Account/Login", {
		email: email.value,
		key: password.value,
		provider: 1
	}).then(res => res.json()).then(data => {
		

		return;
	}).catch(error => {
		console.log(error);
	});

	return;
};
</script>

<style scoped>
form {
	display: flex;
	flex-direction: column;
	align-items: center;

	span {
		margin-top: 25px;
	}

	input {
		width: 350px;
	}

	button {
		width: 350px;
		margin-top: 25px;
	}
}
</style>
