<template>
	<h1>Log In</h1>

	<form>
		<Toast position="top-center" />

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
import Button from "primevue/button";
import { useRouter } from "vue-router";
import InputText from "primevue/inputtext";
import FloatLabel from "primevue/floatlabel";
import { useToast } from "primevue/usetoast";

import { useUserStore } from 'stores/user';
import { useApi } from "composables/useApi";
import { useGeneralStore } from "stores/general";

const api = useApi();
const toast = useToast();
const router = useRouter();

const email = ref("");
const password = ref("");

const userStore = useUserStore();
const generalStore = useGeneralStore();

const doLogIn = () => {
	if (email.value.length < 3 || password.value.length < 3) {
		toast.add({
			severity: "error",
			summary: "Error",
			detail: "You must enter credentials to log in",
			life: 5000,
		});

		return;
	}

	api
		.post("/1.1/Account/Login", {
			email: email.value,
			key: password.value,
			provider: 1,
		}, {
			withCredentials: false
		})
		.then(res => {
			if (generalStore.environment === "development") {
				console.log(res);
			}

			userStore.logIn(res.data.userId);

			router.push({ name: "profile" });

			return;
		})
		.catch((error) => {
			console.log(error);

			toast.add({
				severity: "error",
				summary: "Error",
				detail: error.response.data,
				life: 5000,
			});
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
