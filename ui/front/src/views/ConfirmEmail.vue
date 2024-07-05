<template>
	<h1>Confirming Your Email</h1>

	<div>
		{{ router.currentRoute.value.query.token }}
	</div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';

const router = useRouter();
</script>

<style scoped>

</style>
