<template>
	<h1>Register</h1>

	<form v-if="!registerSent">
		<Toast position="top-center" />

		<FloatLabel>
			<InputText v-model="email" type="email" id="register-email" />
			<label for="register-email">Email Address</label>
		</FloatLabel>

		<FloatLabel>
			<InputText v-model="password" type="password" id="register-password" />
			<label for="register-password">Password</label>
		</FloatLabel>

		<span class="toggle-container">
			<label for="register-tos" @click="agreeToTerms">I agree to the Terms of Service</label>
			<ToggleSwitch v-model="agreesToTos" id="register-tos" />
		</span>

		<Button label="Register" @click="doRegister" />
	</form>

	<div v-else>
		{{ message }}
	</div>
</template>

<script setup>
import { ref } from "vue";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import { useApi } from 'composables/useApi';
import FloatLabel from "primevue/floatlabel";
import { useToast } from 'primevue/usetoast';
import ToggleSwitch from "primevue/toggleswitch";
import { useGeneralStore } from 'stores/general';

const api = useApi();
const toast = useToast();
const generalStore = useGeneralStore();

const email = ref("");
const message = ref("");
const password = ref("");
const agreesToTos = ref(false);
const registerSent = ref(false);

const agreeToTerms = () => {
	agreesToTos.value = !agreesToTos.value;

	return;
};

const doRegister = () => {
	if (!agreesToTos.value) {
		toast.add({
			severity: 'error',
			summary: "Error",
			detail: "You must agree to the Terms of Service to register",
			life: 5000,
		});

		return;
	}

	api.post("/1.1/Account/Register", {
			email: email.value,
			key: password.value,
			confirmKey: password.value,
			provider: 1
		}).then(res => {
			if (generalStore.environment === "development") {
				console.log(res);
			}

			if (res.status === 200) {
				registerSent.value = true;
				message.value = "Your registration has been submitted!  Check your email to confirm your account.";

				return;
			}

			return;
		}).catch(error => {
			if (generalStore.environment === "development") {
				console.log(error);
			}

			toast.add({
				severity: 'error',
				summary: "Error",
				detail: error.response.data,
				life: 5000
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

	span.toggle-container {
		display: flex;
		flex-direction: row;
		align-items: center;
		justify-content: center;

		* {
			margin: 5px;
		}

		label {
			cursor: pointer;
		}
	}

	input {
		width: 350px;
	}

	button {
		width: 350px;
		margin-top: 25px;
	}
}

div {
	text-align: center;
}
</style>
