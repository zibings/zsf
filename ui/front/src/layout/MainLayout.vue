<template>
	<div class="app-wrapper">
		<NavBar />

		<main>
			<router-view />
		</main>

		<Toast position="top-center" />
	</div>
</template>

<script setup lang="ts">
import NavBar from "layout/MainLayout/NavBar.vue";
</script>

<style scoped></style>
