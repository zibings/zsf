<template>
	<h1>Log In</h1>

	<form>
		<Toast />

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
import { useRouter } from "vue-router";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import FloatLabel from "primevue/floatlabel";
import { useToast } from "primevue/usetoast";

import { useAuthStore } from "stores/auth";
import { useApi } from "composables/useApi";
import { useGeneralStore } from "stores/general";

const router = useRouter();
const api = useApi();
const toast = useToast();

const email = ref("");
const password = ref("");
const authStore = useAuthStore();
const generalStore = useGeneralStore();

const doLogIn = () => {
	api
		.post("/1.1/Account/Login", {
			email: email.value,
			key: password.value,
			provider: 1,
		})
		.then((data) => {
			if (generalStore.environment === "development") {
				console.log(data);
			}

			authStore.isLoggedIn = true;
			generalStore.currentUser.userId = data.data.userId;

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
