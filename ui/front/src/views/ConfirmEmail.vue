<template>
	<h1>Confirming Your Email</h1>

	<div>
		{{ message }}
	</div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useApi } from 'composables/useApi';
import { useGeneralStore } from 'stores/general';

const props = defineProps<{
	token: string
}>();

const api = useApi();
const generalStore = useGeneralStore();

const message = ref("");

api.post("/1.1/Account/Confirm", {
	token: props.token
}, {
	withCredentials: false
}).then(res => {
	if (generalStore.environment == "development") {
		console.log(res);
	}

	if (res.status !== 200) {
		message.value = res.data;

		return;
	}

	message.value = "Thanks for confirming your email address!";

	return;
}).catch(error => {
	if (generalStore.environment === "development") {
		console.log(error);
	}

	message.value = "Failed to submit request, please check your link and try again.";

	return;
});
</script>

<style scoped>
div {
	text-align: center;
}
</style>
