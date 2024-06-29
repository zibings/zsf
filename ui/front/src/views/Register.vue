<template>
	<h1>Register</h1>

	<form>
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

		<Button label="Register" />
	</form>
</template>

<script setup>
import { ref } from "vue";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import FloatLabel from "primevue/floatlabel";
import ToggleSwitch from "primevue/toggleswitch";

const email = ref("");
const password = ref("");
const agreesToTos = ref(false);

const agreeToTerms = () => {
	agreesToTos.value = !agreesToTos.value;

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
</style>
