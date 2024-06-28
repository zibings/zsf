<script setup lang="ts">
import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import { ref } from 'vue';

const email = ref('');
</script>

<template>
	<h1>Log In</h1>

	<form>
		<FloatLabel>
			<InputText v-model="email" type="text" id="login-email" label="Email" />
			<label for="login-email">Email Address</label>
		</FloatLabel>

		<FloatLabel>
			<InputText type="password" id="login-password" label="Password" />
			<label for="login-password">Password</label>
		</FloatLabel>

		<Button label="Log In" />
	</form>
</template>

<style scoped>
h1 {
	text-align: center;
	margin-bottom: 25px;
}

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