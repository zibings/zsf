<template>
	<h1>Confirming Your Email</h1>

	<div>
		{{ props.token }}
	</div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';

const props = defineProps<{
	token: string
}>();

const router = useRouter();
</script>

<style scoped>

</style>
