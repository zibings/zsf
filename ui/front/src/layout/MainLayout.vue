<template>
	<div class="app-wrapper">
		<NavBar />

		<main>
			<router-view />
		</main>
	</div>
</template>

<script setup lang="ts">
import NavBar from 'layout/MainLayout/NavBar.vue';
</script>

<style scoped>

</style>
