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
import router from "@/router";
import { useAuthStore } from "@/stores/auth";
import Button from "primevue/button";
import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import { ref } from "vue";

const email = ref("");
const password = ref("");
const authStore = useAuthStore();

const doLogIn = () => {
	authStore.logIn(email.value, password.value);
	router.push("/profile");

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
