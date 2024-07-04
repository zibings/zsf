<template>
	<div class="app-wrapper">
		<NavBar />

		<main>
			<h1>Howdy, Partner</h1>
		</main>

		<router-view />
	</div>
</template>

<script setup lang="ts">
import NavBar from 'layout/MainLayout/NavBar.vue';
</script>

<style scoped>

</style>
